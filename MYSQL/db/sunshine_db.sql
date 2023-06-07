-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2023 at 10:48 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sunshine_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `1_group_totals`
--

CREATE TABLE IF NOT EXISTS `1_group_totals` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `location_id` int(11) NOT NULL DEFAULT '0',
  `location_group_id` int(11) NOT NULL DEFAULT '0',
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`location_id`,`location_group_id`,`lots_number`,`expired_date`),
  KEY `index_keys` (`product_id`,`location_id`,`location_group_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `1_group_total_details`
--

CREATE TABLE IF NOT EXISTS `1_group_total_details` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_group_id` int(11) NOT NULL DEFAULT '0',
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_so_free` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pos_free` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_cm_free` decimal(15,3) DEFAULT '0.000',
  `total_to_in` decimal(15,3) DEFAULT '0.000',
  `total_to_out` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  `date` date NOT NULL,
  PRIMARY KEY (`product_id`,`location_group_id`,`date`),
  KEY `index_key` (`product_id`,`location_group_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `1_inventories`
--

CREATE TABLE IF NOT EXISTS `1_inventories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint(20) DEFAULT NULL,
  `consignment_return_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_return_id` bigint(20) DEFAULT NULL,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `cycle_product_detail_id` bigint(20) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `point_of_sales_id` bigint(20) DEFAULT NULL,
  `credit_memo_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `purchase_return_id` bigint(20) DEFAULT NULL,
  `transfer_order_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_group_id` int(11) NOT NULL,
  `qty` decimal(15,3) NOT NULL,
  `unit_cost` decimal(18,9) DEFAULT '0.000000000',
  `unit_price` decimal(15,4) DEFAULT '0.0000',
  `date` date NOT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `date_expired` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` bigint(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` bigint(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `location_id` (`location_id`),
  KEY `lots_number` (`lots_number`),
  KEY `qty` (`qty`),
  KEY `location_group_id` (`location_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `1_inventory_totals`
--

CREATE TABLE IF NOT EXISTS `1_inventory_totals` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`location_id`,`lots_number`,`expired_date`),
  KEY `index_keys` (`product_id`,`location_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `1_inventory_total_details`
--

CREATE TABLE IF NOT EXISTS `1_inventory_total_details` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_to_in` decimal(15,3) DEFAULT '0.000',
  `total_to_out` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  `date` date NOT NULL,
  PRIMARY KEY (`product_id`,`location_id`,`lots_number`,`expired_date`,`date`),
  KEY `index_keys` (`product_id`,`location_id`,`lots_number`,`expired_date`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `2_group_totals`
--

CREATE TABLE IF NOT EXISTS `2_group_totals` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `location_id` int(11) NOT NULL DEFAULT '0',
  `location_group_id` int(11) NOT NULL DEFAULT '0',
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`location_id`,`location_group_id`,`lots_number`,`expired_date`),
  KEY `index_keys` (`product_id`,`location_id`,`location_group_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `2_group_total_details`
--

CREATE TABLE IF NOT EXISTS `2_group_total_details` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_group_id` int(11) NOT NULL DEFAULT '0',
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_so_free` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pos_free` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_cm_free` decimal(15,3) DEFAULT '0.000',
  `total_to_in` decimal(15,3) DEFAULT '0.000',
  `total_to_out` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  `date` date NOT NULL,
  PRIMARY KEY (`product_id`,`location_group_id`,`date`),
  KEY `index_key` (`product_id`,`location_group_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `2_inventories`
--

CREATE TABLE IF NOT EXISTS `2_inventories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint(20) DEFAULT NULL,
  `consignment_return_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_return_id` bigint(20) DEFAULT NULL,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `cycle_product_detail_id` bigint(20) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `point_of_sales_id` bigint(20) DEFAULT NULL,
  `credit_memo_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `purchase_return_id` bigint(20) DEFAULT NULL,
  `transfer_order_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_group_id` int(11) NOT NULL,
  `qty` decimal(15,3) NOT NULL,
  `unit_cost` decimal(18,9) DEFAULT '0.000000000',
  `unit_price` decimal(15,4) DEFAULT '0.0000',
  `date` date NOT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `date_expired` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` bigint(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` bigint(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `location_id` (`location_id`),
  KEY `lots_number` (`lots_number`),
  KEY `qty` (`qty`),
  KEY `location_group_id` (`location_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `2_inventory_totals`
--

CREATE TABLE IF NOT EXISTS `2_inventory_totals` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`location_id`,`lots_number`,`expired_date`),
  KEY `index_keys` (`product_id`,`location_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `2_inventory_total_details`
--

CREATE TABLE IF NOT EXISTS `2_inventory_total_details` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_to_in` decimal(15,3) DEFAULT '0.000',
  `total_to_out` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  `date` date NOT NULL,
  PRIMARY KEY (`product_id`,`location_id`,`lots_number`,`expired_date`,`date`),
  KEY `index_keys` (`product_id`,`location_id`,`lots_number`,`expired_date`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) DEFAULT NULL,
  `account_type_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `accounts`
--
DROP TRIGGER IF EXISTS `zAccountBfInsert`;
DELIMITER //
CREATE TRIGGER `zAccountBfInsert` BEFORE INSERT ON `accounts`
 FOR EACH ROW BEGIN
	IF NEW.product_id = "" OR NEW.product_id = NULL OR NEW.account_type_id = "" OR NEW.account_type_id = NULL OR NEW.chart_account_id = "" OR NEW.chart_account_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_closing_dates`
--

CREATE TABLE IF NOT EXISTS `account_closing_dates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `account_closing_dates`
--

INSERT INTO `account_closing_dates` (`id`, `date`, `created`, `created_by`) VALUES
(1, '2013-01-01', '2015-02-17 14:31:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE IF NOT EXISTS `account_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `name`, `chart_account_id`, `ordering`, `status`) VALUES
(1, 'Inventory Asset Account', 14, 1, 1),
(2, 'COGS Account', 55, 2, 1),
(3, 'Inventory Adjustment Account', 56, 3, 1),
(4, 'Scrapped Inventory Account', NULL, 4, 0),
(5, 'Internal Use Account', NULL, 5, 0),
(6, 'Cash & Bank Account for Receive Payment', 2, 6, 1),
(7, 'Accounts Receivable', 9, 7, 1),
(8, 'Sales Income', 51, 8, 1),
(9, 'Service Account', 99, 15, 1),
(10, 'Miscellaneous Account', 100, 16, 1),
(11, 'Sales Discount', 53, 9, 1),
(12, 'Sales Change', 93, 11, 1),
(13, 'Cash & Bank Account for Pay Bill', 2, 12, 1),
(14, 'Accounts Payable', 35, 13, 1),
(15, 'Purchase Discount', 90, 14, 1),
(17, 'Sales Markup', 93, 10, 1),
(18, 'Landing Costs', NULL, 17, 0),
(19, 'Cash for Expense', 2, 17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `age_for_labos`
--

CREATE TABLE IF NOT EXISTS `age_for_labos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from` double DEFAULT NULL,
  `to` double DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `age_for_labos`
--

INSERT INTO `age_for_labos` (`id`, `name`, `sex`, `from`, `to`, `created`, `created_by`, `is_active`) VALUES
(1, 'Defult Patient', '', 0, 0, '2012-12-06 16:39:41', 49, 1),
(2, '1 Month - 3 Month', '', 1, 3, '2012-12-06 14:16:15', 49, 1),
(3, 'Boy (6 Year - 15 Year)', 'M', 72, 180, '2012-12-06 16:16:31', 49, 1),
(4, 'Girl (6 Year - 15 Year)', 'F', 72, 180, '2012-12-06 16:29:46', 49, 1),
(5, 'Adult Male', 'M', 180, 2400, '2012-12-06 16:34:16', 49, 1),
(6, 'Adult Female', 'F', 180, 2400, '2012-12-06 16:34:46', 49, 1),
(7, 'Female (3 Month - 6 Year) ', 'F', 3, 72, '2012-12-19 11:58:24', 58, 1),
(8, 'Male (3 Month - 6 Year)', 'M', 3, 72, '2012-12-06 14:16:36', 49, 1),
(12, 'Man 20-30', 'M', 240, 360, '2015-12-04 11:25:24', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `antibiograms`
--

CREATE TABLE IF NOT EXISTS `antibiograms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_request_id` int(11) DEFAULT NULL,
  `labo_item_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `stutus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `antibiograms`
--

INSERT INTO `antibiograms` (`id`, `labo_request_id`, `labo_item_id`, `created`, `created_by`, `stutus`) VALUES
(1, 32, 182, '2018-10-04 18:02:57', 8, 1),
(2, 34, 196, '2018-10-04 18:02:57', 8, 1),
(3, 39, 182, '2018-10-06 11:43:09', 8, 1),
(4, 42, 182, '2018-10-06 17:20:41', 8, 1),
(5, 53, 182, '2018-10-12 10:14:49', 8, 1),
(6, 147, 182, '2018-10-14 09:45:25', 8, 1),
(7, 147, 182, '2018-10-14 09:45:36', 8, 1),
(8, 147, 182, '2018-10-14 09:45:39', 8, 1),
(9, 157, 182, '2018-10-18 12:23:18', 8, 1),
(10, 183, 182, '2018-10-23 14:06:12', 8, 1),
(11, 198, 182, '2018-10-23 14:11:59', 8, 1),
(12, 198, 182, '2018-10-23 14:12:07', 8, 1),
(13, 199, 182, '2018-10-23 14:13:34', 8, 1),
(14, 210, 182, '2018-10-23 15:20:49', 8, 1),
(15, 217, 182, '2018-10-23 16:29:02', 8, 1),
(16, 253, 182, '2018-10-24 09:21:58', 8, 1),
(17, 263, 182, '2018-10-24 11:00:12', 8, 1),
(18, 243, 182, '2018-10-24 12:26:09', 8, 1),
(19, 287, 182, '2018-10-24 20:21:33', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `antibiogram_details`
--

CREATE TABLE IF NOT EXISTS `antibiogram_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) DEFAULT NULL,
  `antibiogram_id` int(11) DEFAULT NULL,
  `resistance` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intermidiate` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sensible` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `queue_id` int(11) DEFAULT NULL,
  `queue_doctor_id` int(11) DEFAULT NULL,
  `app_date` datetime DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_close` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `app_date` (`app_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ap_agings`
--

CREATE TABLE IF NOT EXISTS `ap_agings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`vendor_id`),
  KEY `searchs` (`date`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ap_aging_details`
--

CREATE TABLE IF NOT EXISTS `ap_aging_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ap_aging_id` bigint(20) DEFAULT NULL,
  `general_ledger_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `amount_due` decimal(15,3) DEFAULT NULL,
  `paid` decimal(15,3) DEFAULT NULL,
  `discount` decimal(15,3) DEFAULT NULL,
  `balance` decimal(15,3) DEFAULT NULL,
  `memo` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `filters` (`ap_aging_id`,`general_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ar_agings`
--

CREATE TABLE IF NOT EXISTS `ar_agings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `cgroup_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `egroup_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`cgroup_id`,`customer_id`,`egroup_id`,`employee_id`),
  KEY `searchs` (`date`,`reference`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ar_aging_details`
--

CREATE TABLE IF NOT EXISTS `ar_aging_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ar_aging_id` bigint(20) DEFAULT NULL,
  `general_ledger_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `amount_due` decimal(15,3) DEFAULT NULL,
  `paid` decimal(15,3) DEFAULT NULL,
  `discount` decimal(15,3) DEFAULT NULL,
  `balance` decimal(15,3) DEFAULT NULL,
  `memo` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `filters` (`ar_aging_id`,`general_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `back_up_antibiogram_details`
--

CREATE TABLE IF NOT EXISTS `back_up_antibiogram_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) DEFAULT NULL,
  `antibiogram_id` int(11) DEFAULT NULL,
  `resistance` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intermidiate` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sensible` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `back_up_labo_requests`
--

CREATE TABLE IF NOT EXISTS `back_up_labo_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `labo_id` bigint(20) DEFAULT NULL,
  `labo_item_group_id` bigint(20) DEFAULT NULL,
  `request` text COLLATE utf8_unicode_ci,
  `result` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_type_id` int(11) DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_other` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `long` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lat` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `address_other` text COLLATE utf8_unicode_ci,
  `currency_center_id` int(11) DEFAULT NULL,
  `pos_currency_id` int(11) DEFAULT NULL,
  `work_start` time DEFAULT NULL,
  `work_end` time DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `act` tinyint(4) DEFAULT '1' COMMENT '1: Edit; 2:Update',
  `is_head` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `is_head` (`is_head`),
  KEY `searchs` (`name`,`is_active`,`company_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `sys_code`, `company_id`, `branch_type_id`, `name`, `name_other`, `telephone`, `email_address`, `fax_number`, `long`, `lat`, `country_id`, `province_id`, `district_id`, `commune_id`, `village_id`, `address`, `address_other`, `currency_center_id`, `pos_currency_id`, `work_start`, `work_end`, `created`, `created_by`, `modified`, `modified_by`, `act`, `is_head`, `is_active`) VALUES
(1, '008dd5fb9270a1ead2c83e6044841bd9', 1, 1, 'Sunshine Kids Clinic', 'គ្លីនិកកុមារ​ សាន់សាញ', 'Office Phone: 023 232 323', '', '', '', '', 36, NULL, NULL, NULL, NULL, '# 15-17,Street  598, Sangkat Phnom Penh Thmey , Khan Russei Keo , Phnom Penh, Cambdia.', '# 15-17,Street  598, Sangkat Phnom Penh Thmey , Khan Russei Keo , Phnom Penh, Cambdia.', 1, 1, '07:00:00', '20:00:00', '2017-08-22 11:41:32', 1, '2023-06-07 11:52:42', 1, 1, 1, 1);

--
-- Triggers `branches`
--
DROP TRIGGER IF EXISTS `zBranchAfInsert`;
DELIMITER //
CREATE TRIGGER `zBranchAfInsert` AFTER INSERT ON `branches`
 FOR EACH ROW BEGIN
	INSERT INTO product_branches (branch_id, product_id) SELECT NEW.id, id FROM products WHERE company_id = NEW.company_id;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zBranchBfInsert`;
DELIMITER //
CREATE TRIGGER `zBranchBfInsert` BEFORE INSERT ON `branches`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.branch_type_id = "" OR NEW.branch_type_id = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_other = "" OR NEW.name_other = NULL OR NEW.work_start = "" OR NEW.work_start = NULL OR NEW.work_end = "" OR NEW.work_end = NULL OR NEW.address = "" OR NEW.address = NULL OR NEW.address_other = "" OR NEW.address_other = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zBranchBfUpdate`;
DELIMITER //
CREATE TRIGGER `zBranchBfUpdate` BEFORE UPDATE ON `branches`
 FOR EACH ROW BEGIN
	DECLARE isSale TINYINT(4);
	DECLARE isOrder TINYINT(4);
	DECLARE isQuote TINYINT(4);
	DECLARE isCM TINYINT(4);
	DECLARE isPO TINYINT(4);
	DECLARE isPB TINYINT(4);
	DECLARE isBR TINYINT(4);
	DECLARE isAdj TINYINT(4);
	DECLARE isRS TINYINT(4);
	DECLARE isTO TINYINT(4);
	DECLARE isCusCsm TINYINT(4);
	DECLARE isCusRCsm TINYINT(4);
	DECLARE isLandCost TINYINT(4);
	DECLARE isVenCsm TINYINT(4);
	DECLARE isVenRCsm TINYINT(4);
	DECLARE isJournal TINYINT(4);
	IF NEW.act = 2 THEN
		SELECT COUNT(id) INTO isSale FROM sales_orders WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isOrder FROM orders WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isQuote FROM quotations WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isCM FROM credit_memos WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isPO FROM purchase_requests WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isPB FROM purchase_orders WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isBR FROM purchase_returns WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isAdj FROM cycle_products WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isRS FROM request_stocks WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isTO FROM transfer_orders WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isCusCsm FROM consignments WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isCusRCsm FROM consignment_returns WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isLandCost FROM landing_costs WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isVenCsm FROM vendor_consignments WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(id) INTO isVenRCsm FROM vendor_consignment_returns WHERE branch_id = OLD.id AND status > 0 LIMIT 1;
		SELECT COUNT(general_ledger_details.id) INTO isJournal FROM general_ledger_details INNER JOIN general_ledgers ON general_ledgers.id = general_ledger_details.general_ledger_id WHERE general_ledger_details.branch_id = OLD.id AND general_ledgers.is_active = 1 LIMIT 1;
		IF isSale > 0 OR isOrder > 0 OR isQuote > 0 OR isCM > 0 OR isPO > 0 OR isPB > 0 OR isBR > 0 OR isAdj > 0 OR isRS > 0 OR isTO > 0 OR isCusCsm > 0 OR isCusRCsm > 0 OR isLandCost > 0 OR isVenCsm > 0 OR isVenRCsm > 0 OR isJournal > 0 THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Data could not been delete';
		END IF;
	ELSE
		IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.branch_type_id = "" OR NEW.branch_type_id = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_other = "" OR NEW.name_other = NULL OR NEW.work_start = "" OR NEW.work_start = NULL OR NEW.work_end = "" OR NEW.work_end = NULL OR NEW.address = "" OR NEW.address = NULL OR NEW.address_other = "" OR NEW.address_other = NULL THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
		END IF;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `branch_currencies`
--

CREATE TABLE IF NOT EXISTS `branch_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `rate_to_sell` decimal(15,9) DEFAULT '0.000000000',
  `rate_to_change` decimal(15,9) DEFAULT '0.000000000',
  `rate_purchase` decimal(15,9) DEFAULT '0.000000000',
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_pos_default` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_searchs` (`branch_id`,`currency_center_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `branch_currencies`
--

INSERT INTO `branch_currencies` (`id`, `sys_code`, `branch_id`, `currency_center_id`, `exchange_rate_id`, `rate_to_sell`, `rate_to_change`, `rate_purchase`, `created`, `created_by`, `modified`, `modified_by`, `is_pos_default`, `is_active`) VALUES
(1, '041826786bd9fefcf564c9e010f0d6b6', 1, 2, 8, '4000.000000000', '4000.000000000', '4000.000000000', '2017-11-06 15:37:53', 1, '2019-05-03 16:16:30', 1, 1, 1);

--
-- Triggers `branch_currencies`
--
DROP TRIGGER IF EXISTS `zBranchCurrencyBfInsert`;
DELIMITER //
CREATE TRIGGER `zBranchCurrencyBfInsert` BEFORE INSERT ON `branch_currencies`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.branch_id = "" OR NEW.branch_id = NULL OR NEW.currency_center_id = "" OR NEW.currency_center_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `branch_types`
--

CREATE TABLE IF NOT EXISTS `branch_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `search` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `branch_types`
--

INSERT INTO `branch_types` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '108de0138a3c639a32e153a442560811', 'Branch', '2016-11-04 13:49:31', 1, '2016-11-04 13:49:31', NULL, 1);

--
-- Triggers `branch_types`
--
DROP TRIGGER IF EXISTS `zBranchTypeBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zBranchTypeBeforeDelete` BEFORE DELETE ON `branch_types`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete this branch type';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zBranchTypeBeforeUpdate`;
DELIMITER //
CREATE TRIGGER `zBranchTypeBeforeUpdate` BEFORE UPDATE ON `branch_types`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete/update this branch type';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zBranchTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zBranchTypeBfInsert` BEFORE INSERT ON `branch_types`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '47116be244ddab563161c8072ae7d11f', 'Korea', '2019-05-03 16:10:12', 1, '2019-05-03 16:10:12', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `budget_pls`
--

CREATE TABLE IF NOT EXISTS `budget_pls` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `year` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `year` (`year`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `budget_pl_details`
--

CREATE TABLE IF NOT EXISTS `budget_pl_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `budget_pl_id` bigint(20) DEFAULT NULL,
  `chart_account_id` bigint(20) DEFAULT NULL,
  `m1` decimal(15,3) DEFAULT NULL,
  `m2` decimal(15,3) DEFAULT NULL,
  `m3` decimal(15,3) DEFAULT NULL,
  `m4` decimal(15,3) DEFAULT NULL,
  `m5` decimal(15,3) DEFAULT NULL,
  `m6` decimal(15,3) DEFAULT NULL,
  `m7` decimal(15,3) DEFAULT NULL,
  `m8` decimal(15,3) DEFAULT NULL,
  `m9` decimal(15,3) DEFAULT NULL,
  `m10` decimal(15,3) DEFAULT NULL,
  `m11` decimal(15,3) DEFAULT NULL,
  `m12` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budget_pl_id` (`budget_pl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cache_datas`
--

CREATE TABLE IF NOT EXISTS `cache_datas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cache_datas`
--

INSERT INTO `cache_datas` (`id`, `type`, `modified`) VALUES
(1, 'Products', '2023-05-31 16:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `cgroups`
--

CREATE TABLE IF NOT EXISTS `cgroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `user_apply` tinyint(4) DEFAULT '0' COMMENT '0: All; 1: Customize',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cgroups`
--

INSERT INTO `cgroups` (`id`, `sys_code`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `user_apply`, `is_active`) VALUES
(1, '0ea479a9bcbb44100ca1ceb6939cce11', 'General', NULL, '2017-07-21 15:36:55', 1, '2017-07-21 15:36:55', NULL, 0, 1);

--
-- Triggers `cgroups`
--
DROP TRIGGER IF EXISTS `zCgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zCgroupBfInsert` BEFORE INSERT ON `cgroups`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cgroup_companies`
--

CREATE TABLE IF NOT EXISTS `cgroup_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cgroup_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cgroup_id` (`cgroup_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cgroup_companies`
--

INSERT INTO `cgroup_companies` (`id`, `cgroup_id`, `company_id`) VALUES
(1, 1, 1);

--
-- Triggers `cgroup_companies`
--
DROP TRIGGER IF EXISTS `zCgroupCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zCgroupCompanyBfInsert` BEFORE INSERT ON `cgroup_companies`
 FOR EACH ROW BEGIN
	IF NEW.cgroup_id = "" OR NEW.cgroup_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cgroup_price_types`
--

CREATE TABLE IF NOT EXISTS `cgroup_price_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cgroup_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cgroup_id` (`cgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `cgroup_price_types`
--
DROP TRIGGER IF EXISTS `zCgroupPriceTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zCgroupPriceTypeBfInsert` BEFORE INSERT ON `cgroup_price_types`
 FOR EACH ROW BEGIN
	IF NEW.cgroup_id = "" OR NEW.cgroup_id = NULL OR NEW.price_type_id = "" OR NEW.price_type_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chart_accounts`
--

CREATE TABLE IF NOT EXISTS `chart_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `chart_account_type_id` int(11) DEFAULT NULL,
  `chart_account_group_id` int(11) DEFAULT NULL,
  `account_codes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manual` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `chart_account_type_id` (`chart_account_type_id`),
  KEY `chart_account_group_id` (`chart_account_group_id`),
  KEY `account_codes` (`account_codes`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=105 ;

--
-- Dumping data for table `chart_accounts`
--

INSERT INTO `chart_accounts` (`id`, `sys_code`, `parent_id`, `chart_account_type_id`, `chart_account_group_id`, `account_codes`, `account_description`, `manual`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'a4725cf2785eac9c3b4b43274b49e418', NULL, 1, 1, '100000', 'Cash and Bank', '', '2014-10-17 08:29:12', 1, '2014-11-17 16:45:44', 1, 1),
(2, '642756c8de9db4cb26136eabca0b7320', 1, 1, 17, '100100', 'Cash on Hand', '', '2014-10-17 08:33:03', 1, '2014-11-17 16:46:53', 1, 1),
(3, '50997e5e846a7c3cbfb42b1c4e9cb0fa', 1, 1, 1, '100200', 'Petty Cash', '', '2014-10-17 08:38:31', 1, '2014-11-17 16:47:08', 1, 1),
(4, 'c51018bdf358f390139a55230b08a63a', 1, 1, 1, '100300', 'Cash in Bank', '', '2014-10-17 08:39:06', 1, '2014-11-17 16:47:41', 1, 1),
(5, '55468d8a4669b32ee79eeae4b5f360db', 4, 1, 1, '100310', 'Cash in Bank-Canadia Bank', '', '2014-10-17 08:40:38', 1, '2014-11-17 16:47:53', 1, 1),
(6, 'bb5405c2441c74330da4759871a32c23', 4, 1, 1, '100320', 'Cash in Bank-ACLEDA Bank', '', '2014-10-17 08:41:14', 1, '2014-11-17 16:48:06', 1, 1),
(7, 'de8979d8984ba3d84ded389fae52677a', 4, 1, 1, '100330', 'Cash in Bank DBS Bank', '', '2014-10-17 08:41:56', 1, '2014-11-17 16:49:17', 1, 1),
(8, 'd6e9f099902ade5a17f329430b1b4909', NULL, 2, 2, '110000', 'Accounts Receivable', '', '2014-10-17 08:43:10', 1, '2014-11-17 16:48:31', 1, 1),
(9, 'b84e4c6715f55f1f14600be8a4e7152f', 8, 2, 2, '110100', 'Accounts Receivable', '', '2014-10-17 08:43:46', 1, '2014-11-17 16:49:30', 1, 1),
(10, '505c5b9868bd50dc032a9a2f54fd520e', 8, 2, 2, '110200', 'Other Receivables', '', '2014-10-17 08:44:31', 1, '2014-11-17 16:49:44', 1, 1),
(11, '05280585363e0772e42f63a78b974c37', 8, 2, 2, '110300', 'Allowance for Doubtful Account', '', '2014-10-17 08:45:25', 1, '2014-11-17 16:50:00', 1, 1),
(12, '744a563327799d3b586f24f1dced939e', 8, 2, 25, '110400', 'Staff Advance', '', '2014-10-17 08:46:08', 1, '2014-11-17 16:50:14', 1, 1),
(13, 'ff3a4cca07f3838626fed9ac2e125a03', 8, 2, 25, '110500', 'Notes Receivable', '', '2014-10-17 08:47:01', 1, '2014-11-17 16:50:28', 1, 1),
(14, '93058e8c8160e4c37a544eedf6c67fd8', NULL, 3, 19, '120000', 'Inventory', '', '2014-10-17 08:49:19', 1, '2014-11-17 16:50:40', 1, 1),
(15, '1272722435651653666fcf8a578979cd', NULL, 3, 3, '140100', 'Prepaid Profit Tax', '', '2014-10-17 08:49:51', 1, '2014-11-17 16:50:55', 1, 1),
(16, 'ed98f33b09b8f3b5c810febfb05c7254', NULL, 3, 3, '140200', 'Prepaid Expenses', '', '2014-10-17 08:50:24', 1, '2014-11-17 16:51:09', 1, 1),
(17, '3806cd82de36a8cd9bea9b3732815ffc', NULL, 3, 3, '140300', 'Prepaid Insurance', '', '2014-10-17 08:50:57', 1, '2014-11-17 16:51:23', 1, 1),
(18, '861d6db6c70cd01843f1245282bc1f00', NULL, 3, 3, '140400', 'Deposits', '', '2014-10-17 08:51:37', 1, '2014-11-17 16:51:39', 1, 1),
(19, '3ac842235d508628a184c98254dbe92d', NULL, 4, 4, '151000', 'Furniture and Fixtures', '', '2014-10-17 08:52:08', 1, '2014-11-17 16:51:57', 1, 1),
(20, 'f7e9217dd3e3dca0a3633aeed652492c', 19, 4, 4, '151100', 'Cost of Furniture and Fixtures', '', '2014-10-17 08:53:31', 1, '2014-11-17 16:52:14', 1, 1),
(21, 'fc1b5aa8ceb6858065aa634b311f3a21', 19, 4, 4, '151200', 'Accum. Depreciation of Furnitur', '', '2014-10-17 08:54:09', 1, '2014-11-17 16:52:29', 1, 1),
(22, '5aafa1770a4b4c00de1920ca63194639', NULL, 4, 4, '152000', 'Office Equipment', '', '2014-10-17 08:54:40', 1, '2014-11-17 16:52:45', 1, 1),
(23, '473b1d8e6d9f9e105a1e5f62e06a597c', 22, 4, 4, '152100', 'Cost of Office Equipment', '', '2014-10-17 08:55:23', 1, '2014-11-17 16:52:58', 1, 1),
(24, '20ac5cbcebc02d531ce96b8ab905c195', 22, 4, 4, '152200', 'Accum. Depreciation of Office Equipment', '', '2014-10-17 08:56:02', 1, '2014-11-17 16:53:15', 1, 1),
(25, '1b33173033d65ee9e9d0b71e8cae5976', NULL, 4, 4, '153000', 'Automobile', '', '2014-10-17 08:56:34', 1, '2014-11-17 16:53:30', 1, 1),
(26, '8bd4d86d695810af05eacf40f53e67a6', 25, 4, 4, '153100', 'Cost of Automobile', '', '2014-10-17 08:57:21', 1, '2014-11-17 17:22:15', 1, 1),
(27, '49f713b8ba5349788b47c3633f003110', 25, 4, 4, '153200', 'Accum. Depreciation of Automobile', '', '2014-10-17 08:58:06', 1, '2014-11-17 17:30:22', 1, 1),
(28, '607246c20d43a516e64bbd0202645cea', NULL, 4, 4, '154000', 'Building', '', '2014-10-17 08:58:46', 1, '2014-10-17 08:58:46', NULL, 1),
(29, 'b1b1353b8abe7f5de2b18bff2e5a7675', 28, 4, 4, '154100', 'Cost of Building', '', '2014-10-17 08:59:27', 1, '2014-10-17 08:59:27', NULL, 1),
(30, 'fbc19d3eee97f0b34efa8b5b90bd3ac8', 28, 4, 4, '154200', 'Accum. Depreciation of Building', '', '2014-10-17 09:00:07', 1, '2014-10-17 09:00:07', NULL, 1),
(31, 'c9e3acd37dce077b11adea6bb01e350a', NULL, 4, 4, '155000', 'Software', '', '2014-10-17 09:00:42', 1, '2014-10-17 09:01:20', 1, 1),
(32, 'f2232b5efe4793abde610034e9dee9a4', 31, 4, 4, '155100', 'Cost of Software', '', '2014-10-17 09:02:03', 1, '2014-10-17 09:02:03', NULL, 1),
(33, 'bb843defb4e34c02d8aba51d857273ef', 31, 4, 4, '155200', 'Amortization of Software', '', '2014-10-17 09:02:38', 1, '2014-10-17 09:03:02', 1, 1),
(34, '32e4a2a41834ab110b21f96b05060356', NULL, 6, 15, '200000', 'Accounts Payable', '', '2014-10-17 09:06:35', 1, '2014-11-17 17:23:19', 1, 1),
(35, '517ea938a8f2c51bacc6e1bd013fc0ed', 34, 6, 15, '201100', 'Accounts Payable', '', '2014-10-17 09:07:29', 1, '2014-10-17 09:07:29', NULL, 1),
(36, 'fdbf2d0d9b8f54377f9f90604454bbd1', 34, 6, 15, '201300', 'Notes Payable', '', '2014-10-17 09:08:48', 1, '2014-10-17 09:08:48', NULL, 1),
(37, 'bad55a65be315f762f533d82d9405ff3', NULL, 8, 14, '230000', 'Accrued Expenses', '', '2014-10-17 09:09:31', 1, '2014-11-17 17:32:36', 1, 1),
(38, '20a2de56bdbaede29d77772179569f08', 37, 8, 14, '231000', 'Salary Payable', '', '2014-10-17 09:10:21', 1, '2014-10-17 13:56:03', 1, 1),
(39, 'dc038abd3f7cd5e02709a025b0d7bffb', 37, 8, 14, '232000', 'Deposits from Customers', '', '2014-10-17 09:11:04', 1, '2014-10-17 13:56:35', 1, 1),
(40, '63d27dd15ffb562a4bd981038c50ed0f', 37, 8, 14, '233000', 'Office Rental Payable', '', '2014-10-17 09:31:46', 1, '2014-10-17 13:57:54', 1, 1),
(41, '89f5487b3d41845bbd9eb780bd33bfe7', 37, 8, 14, '234000', 'Interest Payable', '', '2014-10-17 09:33:48', 1, '2014-10-17 13:58:14', 1, 1),
(42, '26fd706a1114c53588e58f94d06a4e5e', NULL, 9, 13, '270000', 'Long Term Liabilities', '', '2014-10-17 09:35:03', 1, '2014-10-17 09:35:03', NULL, 1),
(43, '0d891f0d408f593f1740c1d3fb54001a', 42, 9, 13, '270100', 'Loans Payable', '', '2014-10-17 09:37:43', 1, '2014-10-17 09:37:43', NULL, 1),
(44, '3eb4d838889afd250ca764ac376647c1', 42, 9, 13, '270200', 'Fixd Loan', '', '2014-10-17 09:40:11', 1, '2014-10-17 09:40:11', NULL, 1),
(45, 'b017edb42189a6b0fbb2655590d43cea', NULL, 10, 20, '310000', 'Equity', '', '2014-10-17 09:49:50', 1, '2014-10-17 09:49:50', NULL, 1),
(46, '7c10afb0d35c57a85b329ec7daf2ce84', 45, 10, 20, '310100', 'Equity', '', '2014-10-17 09:51:30', 1, '2014-10-17 09:51:30', NULL, 1),
(47, 'b492ea551df5ca829c4c6a8148759b50', 45, 10, 20, '320100', 'Dividends Paid', '', '2014-10-17 09:52:10', 1, '2014-10-17 09:52:10', NULL, 1),
(48, '337587d4f910779a23338a8d1d656196', 45, 10, 20, '320200', 'Owner Withdrawal', '', '2014-10-17 09:52:54', 1, '2014-10-17 09:53:10', 1, 1),
(49, '2e23d572376f7657359ece304dd3d9c7', 45, 10, 20, '330000', 'Retained Earnings', '', '2014-10-17 09:53:45', 1, '2014-10-17 09:53:45', NULL, 1),
(50, '2b87ffd12437b1cebe1396ee0849936f', NULL, 11, 10, '400000', 'Income', '', '2014-10-17 09:56:07', 1, '2014-10-17 09:56:07', NULL, 1),
(51, '8abb6bc00035c14bcf5fd034d163e816', 50, 11, 10, '400100', 'Sales Revenues', '', '2014-10-17 09:56:49', 1, '2014-10-17 09:56:49', NULL, 1),
(52, 'dd0faf5e71811f22292e490d9ec29fa0', 50, 11, 21, '450300', 'Sale Return & Allowend', '', '2014-10-17 09:58:16', 1, '2014-10-17 09:58:16', NULL, 1),
(53, '45ef2bb675cb0d48a999ea773572c795', 50, 11, 21, '450400', 'Sales Discounts', '', '2014-10-17 09:58:54', 1, '2014-10-17 09:58:54', NULL, 1),
(54, '32f3be54e566919d96f69f3561152ebb', NULL, 12, 8, '500000', 'Cost of Goods Sold', '', '2014-10-17 09:59:38', 1, '2014-10-17 09:59:38', NULL, 1),
(55, '78abf96576498545720db669a4194839', 54, 12, 8, '500100', 'Cost of Goods Sold', '', '2014-10-17 10:01:39', 1, '2014-10-17 10:01:39', NULL, 1),
(56, '8d1057a38f95f869c98c71e763c6bbbc', 54, 12, 8, '500200', 'Inventory Adjustment', '', '2014-10-17 10:02:28', 1, '2014-10-17 10:02:28', NULL, 1),
(57, 'd5347e7253b63b37fae33fdd83b6bb4f', NULL, 13, 7, '600000', 'Expenses', '', '2014-10-17 10:03:02', 1, '2014-10-17 10:03:02', NULL, 1),
(58, '83ccbd054963f6530cca8e92f59780d2', 57, 13, 7, '610100', 'Advertising Expense', '', '2014-10-17 10:04:42', 1, '2014-10-17 10:04:42', NULL, 1),
(59, 'fe080eb3061592fa71a97bbfdb6b01b4', 57, 13, 7, '610200', 'Bad Debt Expense', '', '2014-10-17 10:05:32', 1, '2014-10-17 10:05:32', NULL, 1),
(60, '4ca83abe2ecb4a147f1afdce39fab0fd', 57, 13, 7, '610300', 'Bank Charges', '', '2014-10-17 10:06:30', 1, '2014-10-17 10:06:30', NULL, 1),
(61, 'cb0419f8000079dfe1c700b3a6f77609', 57, 13, 7, '610400', 'Patent', '', '2014-10-17 10:07:46', 1, '2014-10-17 10:07:46', NULL, 1),
(62, '9cde135cc995f3087ef8bb7a070b5a8b', 57, 13, 7, '610500', 'Depreciation Expense', '', '2014-10-17 10:08:37', 1, '2014-10-17 10:08:37', NULL, 1),
(63, '67e22cec0f16a938073a2a423d1c5ccb', 57, 13, 7, '610600', 'Profit Tax Expense', '', '2014-10-17 10:09:16', 1, '2014-10-17 10:09:16', NULL, 1),
(64, '2616b84e93c763593e80053b55daeef0', 57, 13, 7, '610700', 'Insurance Expense', '', '2014-10-17 10:10:22', 1, '2014-10-17 10:10:36', 1, 1),
(65, '476b5e94727db3407929f431add0c883', 57, 13, 7, '700100', 'Meals and Entertainment Exp', '', '2014-10-17 10:11:19', 1, '2014-10-17 10:11:19', NULL, 1),
(66, 'd4a58013790d8f003379335a73e75a2a', 57, 13, 7, '700200', 'Meeting and Refreshment', '', '2014-10-17 10:12:06', 1, '2014-10-17 10:12:06', NULL, 1),
(67, 'e55b083a5ff1aa4f344da2716422d76c', 57, 13, 7, '700300', 'Staff Relasionship', '', '2014-10-17 10:12:58', 1, '2014-10-17 10:12:58', NULL, 1),
(68, '56ac5eaa20fabfc4206d1238cb1a4764', 57, 13, 7, '700400', 'Customer Relasionship', '', '2014-10-17 10:13:34', 1, '2014-10-17 10:13:34', NULL, 1),
(69, '6a5ea58538c928654cddbf2d3fe3bcdd', 57, 13, 7, '700500', 'Office Rental Expense', '', '2014-10-17 10:14:18', 1, '2014-10-17 10:14:18', NULL, 1),
(70, '2b16e77cb7fea2e37fb98d61d4f3c4ad', 57, 13, 7, '700700', 'Travel Expense', '', '2014-10-17 10:18:33', 1, '2014-10-17 10:18:33', NULL, 1),
(71, '7312f10b29351322f7a438b0960de513', 57, 13, 7, '700800', 'Lease Expense', '', '2014-10-17 10:19:34', 1, '2014-10-17 10:19:34', NULL, 1),
(72, '0d618f4f05eb5bfcdd3112f8d6103d08', 57, 13, 7, '700900', 'Salary Expense', '', '2014-10-17 10:20:18', 1, '2014-10-17 10:20:18', NULL, 1),
(73, '06139aef5e50a3c655429b1eb5d390f8', 57, 13, 7, '711000', 'Telephone Expense', '', '2014-10-17 10:21:15', 1, '2014-10-17 10:21:15', NULL, 1),
(74, 'aae22fff4c32d8b40cf63e2538116ff1', 57, 13, 7, '712000', 'Internet Expense', '', '2014-10-17 10:22:00', 1, '2014-10-17 10:22:00', NULL, 1),
(75, '26ea11514cf5fc0335393ea00606d002', 57, 13, 7, '713000', 'Interest Expense', '', '2014-10-17 10:24:48', 1, '2014-10-17 10:24:48', NULL, 1),
(76, 'b2359d1426229d8115d249117e1331bf', 57, 13, 7, '714000', 'Securities Expense', '', '2014-10-17 10:25:54', 1, '2014-10-17 10:25:54', NULL, 1),
(77, 'e32ddce91241018b1ab00cc35ed954bd', 57, 13, 7, '715000', 'Office Supply Expense', '', '2014-10-17 13:32:00', 1, '2014-10-17 13:32:00', NULL, 1),
(78, 'd2127d745eaf61d1279369d107b6f6da', 57, 13, 22, '716000', 'Utilities Expense', '', '2014-10-17 13:34:54', 1, '2014-10-17 13:34:54', NULL, 1),
(79, 'cdf1d72dae119b75b3ceeb7cdf309656', 78, 13, 22, '716100', 'Water', '', '2014-10-17 13:36:07', 1, '2014-10-17 13:36:07', NULL, 1),
(80, '1505a45397740a32218f1d20e5b3a4ba', 78, 13, 22, '716200', 'Electricty', '', '2014-10-17 13:36:47', 1, '2014-10-17 13:36:47', NULL, 1),
(81, '343fa4bf161647dc70c9a029cf654f0f', 57, 13, 7, '717000', 'Gasoline Expensse', '', '2014-10-17 13:37:42', 1, '2014-10-17 13:37:42', NULL, 1),
(82, '595426dc66028a703d761612a63a01ec', 57, 13, 7, '718000', 'Transportation Expense', '', '2014-10-17 13:38:20', 1, '2014-10-17 13:38:20', NULL, 1),
(83, '5f05d7f9852b5ea05f027864882e0706', 57, 13, 23, '720000', 'Repairs', '', '2014-10-17 13:39:29', 1, '2014-10-17 13:39:29', NULL, 1),
(84, 'd7f1a5d1a694a83772df6664b50d437c', 83, 13, 23, '720100', 'Automobile Repairs', '', '2014-10-17 13:40:16', 1, '2014-10-17 13:40:16', NULL, 1),
(85, 'c6fbb585dc593f7fab5ac9419e466704', 83, 13, 23, '720200', 'Office Repairs', '', '2014-10-17 13:40:57', 1, '2014-10-17 13:40:57', NULL, 1),
(86, '0787c95d8851f7cf563544603724d851', 83, 13, 23, '720300', 'Building Repairs', '', '2014-10-17 13:41:34', 1, '2014-10-17 13:41:34', NULL, 1),
(87, 'affd6162c2d1814c4e67dd0bf96f99ad', 83, 13, 23, '720400', 'Software Reparirs', '', '2014-10-17 13:42:06', 1, '2014-10-17 13:42:06', NULL, 1),
(88, 'd84817d6cd71102951ea225edf0f7fa9', 83, 13, 23, '720500', 'Office Equipment Repairs', '', '2014-10-17 13:42:50', 1, '2014-10-17 13:42:50', NULL, 1),
(89, '0eb0c148a8d24a9bad900bd6d8dc5387', 83, 13, 23, '720600', 'Other Repairs', '', '2014-10-17 13:43:30', 1, '2014-10-17 13:43:30', NULL, 1),
(90, '84b6ccef8b80d99f0c74deff144bb1ae', 57, 13, 24, '730000', 'Purchase Discounts', '', '2014-10-17 13:44:45', 1, '2014-10-17 13:44:45', NULL, 1),
(91, '6418f8aadbcb7ba35aa490c762c182e1', 57, 13, 24, '740000', 'Purchase Return & Allowend', '', '2014-10-17 13:45:27', 1, '2014-10-17 13:45:27', NULL, 1),
(92, 'ff4f847f364c623250b58450ee8ee5b0', NULL, 14, 6, '800000', 'Other Income', '', '2014-10-17 13:46:10', 1, '2014-10-17 13:46:10', NULL, 1),
(93, '7b093eef7e8d1ffed194b686897ca194', 92, 14, 6, '800100', 'Other Income', '', '2014-10-17 13:46:48', 1, '2014-10-17 13:46:48', NULL, 1),
(94, '48fa443ba4b72b08bc6d627ab70461f0', 92, 14, 6, '800200', 'Interest Income', '', '2014-10-17 13:47:36', 1, '2014-10-17 13:47:36', NULL, 1),
(95, '46ec5644d64cd41fab8f578f28053851', NULL, 15, 5, '900000', 'Other Expense', '', '2014-10-17 13:48:11', 1, '2014-10-17 13:48:11', NULL, 1),
(96, '467ba9a3b84c42b651bc854e4933cc8c', 95, 15, 5, '900100', 'Other Expense', '', '2014-10-17 13:48:44', 1, '2014-10-17 13:48:44', NULL, 1),
(97, '59baba92b0c0d48798239834316c6c25', 95, 15, 5, '900200', 'Bank Charge', '', '2014-10-17 13:49:20', 1, '2014-10-17 13:49:20', NULL, 1),
(99, 'cfcd12517d0e78818466f0237e208569', 50, 11, 10, '402000', 'Service Income', '', '2012-03-06 14:32:56', 11, '2012-08-18 14:55:09', 11, 1),
(100, '7f129dc7b5d82124a0ed1976bb779dbd', 92, 14, 6, '800200', 'Sale Msc. Income', NULL, '2014-10-17 13:46:48', 1, '2014-10-17 13:46:48', NULL, 1),
(101, '1f229cb11a74a7f9760c2ca5eba6f33d', 95, 15, 5, '911300', 'Regular Discount', NULL, '2014-10-17 13:49:20', 1, '2014-10-17 13:49:20', NULL, 1),
(102, '190189cb8554ee43cb15bd8a19bae0cf', 16, 3, 26, '140201', 'VAT Input', '', '2015-05-04 17:03:13', 1, '2015-05-04 17:03:13', NULL, 1),
(103, '5eacba7915360e6e8331ef4870c8db0d', NULL, 8, 14, '210000', 'Liability', '', '2015-05-04 17:09:46', 1, '2015-05-04 17:09:46', NULL, 1),
(104, '8f1d3ec897e59418366ccdebb4d83394', 103, 8, 14, '210100', 'VAT Output', '', '2015-05-04 17:10:46', 1, '2015-05-04 17:10:46', NULL, 1);

--
-- Triggers `chart_accounts`
--
DROP TRIGGER IF EXISTS `zChartAccountBfInsert`;
DELIMITER //
CREATE TRIGGER `zChartAccountBfInsert` BEFORE INSERT ON `chart_accounts`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.chart_account_type_id = "" OR NEW.chart_account_type_id = NULL OR NEW.chart_account_group_id = "" OR NEW.chart_account_group_id = NULL OR NEW.account_codes = "" OR NEW.account_codes = NULL OR NEW.account_description = "" OR NEW.account_description = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chart_account_companies`
--

CREATE TABLE IF NOT EXISTS `chart_account_companies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=104 ;

--
-- Dumping data for table `chart_account_companies`
--

INSERT INTO `chart_account_companies` (`id`, `chart_account_id`, `company_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 102, 1),
(20, 19, 1),
(21, 20, 1),
(22, 21, 1),
(23, 22, 1),
(24, 23, 1),
(25, 24, 1),
(26, 25, 1),
(27, 26, 1),
(28, 27, 1),
(29, 28, 1),
(30, 29, 1),
(31, 30, 1),
(32, 31, 1),
(33, 32, 1),
(34, 33, 1),
(35, 34, 1),
(36, 35, 1),
(37, 36, 1),
(38, 37, 1),
(39, 38, 1),
(40, 39, 1),
(41, 40, 1),
(42, 41, 1),
(43, 103, 1),
(44, 104, 1),
(45, 42, 1),
(46, 43, 1),
(47, 44, 1),
(48, 45, 1),
(49, 46, 1),
(50, 47, 1),
(51, 48, 1),
(52, 49, 1),
(53, 50, 1),
(54, 51, 1),
(55, 52, 1),
(56, 53, 1),
(57, 99, 1),
(58, 54, 1),
(59, 55, 1),
(60, 56, 1),
(61, 57, 1),
(62, 58, 1),
(63, 59, 1),
(64, 60, 1),
(65, 61, 1),
(66, 62, 1),
(67, 63, 1),
(68, 64, 1),
(69, 65, 1),
(70, 66, 1),
(71, 67, 1),
(72, 68, 1),
(73, 69, 1),
(74, 70, 1),
(75, 71, 1),
(76, 72, 1),
(77, 73, 1),
(78, 74, 1),
(79, 75, 1),
(80, 76, 1),
(81, 77, 1),
(82, 78, 1),
(83, 79, 1),
(84, 80, 1),
(85, 81, 1),
(86, 82, 1),
(87, 83, 1),
(88, 84, 1),
(89, 85, 1),
(90, 86, 1),
(91, 87, 1),
(92, 88, 1),
(93, 89, 1),
(94, 90, 1),
(95, 91, 1),
(96, 92, 1),
(97, 93, 1),
(98, 94, 1),
(99, 100, 1),
(100, 95, 1),
(101, 96, 1),
(102, 97, 1),
(103, 101, 1);

--
-- Triggers `chart_account_companies`
--
DROP TRIGGER IF EXISTS `zChartAccountCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zChartAccountCompanyBfInsert` BEFORE INSERT ON `chart_account_companies`
 FOR EACH ROW BEGIN
	IF NEW.chart_account_id = "" OR NEW.chart_account_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chart_account_groups`
--

CREATE TABLE IF NOT EXISTS `chart_account_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chart_account_type_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_depreciation` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `chart_account_type_id` (`chart_account_type_id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

--
-- Dumping data for table `chart_account_groups`
--

INSERT INTO `chart_account_groups` (`id`, `sys_code`, `chart_account_type_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_depreciation`, `is_active`) VALUES
(1, 'd83c64331ee0f0d0a8389ba9db087912', 1, 'Cash at Bank', '2014-03-13 19:47:08', 57, '2014-03-13 19:51:56', 57, 0, 1),
(2, '5128403e8f27e8aef6cf9eb80714aca0', 2, 'Account Receivable', '2014-03-13 19:47:24', 57, '2014-03-13 19:47:24', NULL, 0, 1),
(3, 'f5254a45340539f1b2b18fd08d909100', 3, 'Other current assets', '2014-03-13 19:47:46', 57, '2014-03-13 19:47:46', NULL, 0, 1),
(4, 'f7df4ab216e608ce850e34df881feef4', 4, 'Fixed assets', '2014-03-13 19:48:02', 57, '2014-03-13 19:48:02', NULL, 0, 1),
(5, '08976552a9d8ef839afac6ef7f7f5a44', 15, 'Other expenses', '2014-03-13 19:48:16', 57, '2014-03-13 19:48:16', NULL, 0, 1),
(6, '3c610818f0d9bd75dd167e1eda626189', 14, 'Other income', '2014-03-13 19:48:29', 57, '2014-03-13 19:48:29', NULL, 0, 1),
(7, 'e81846649f4fed3d1e7dd860f4820c34', 13, 'Operation Expenses', '2014-03-13 19:48:40', 57, '2014-10-17 10:03:58', 1, 0, 1),
(8, 'f70b30370202da798e95ddfc51db34f9', 12, 'Cost of Goods Sold', '2014-03-13 19:48:59', 57, '2014-03-13 19:48:59', NULL, 0, 1),
(9, '0524c850d6c47861bcc80b0bcb95e4fa', 12, 'Cost of Sale', '2014-03-13 19:49:10', 57, '2014-03-13 19:49:10', NULL, 0, 1),
(10, '20f40a3b780b37a71f3312d0a2f8d006', 11, 'Income', '2014-03-13 19:49:26', 57, '2014-10-17 14:12:30', 1, 0, 1),
(11, '6f022ae2a88ef6a86d3cb57d3017bb05', 10, 'Share Capital', '2014-03-13 19:49:41', 57, '2014-03-13 19:49:41', NULL, 0, 1),
(12, '84b93bf951b2d68544d6b0a719af34eb', 10, 'Retained Earning', '2014-03-13 19:49:56', 57, '2014-03-13 19:49:56', NULL, 0, 1),
(13, '06db2af8e0f6cb408dfb77a2e55ef419', 9, 'Long-term liabilities', '2014-03-13 19:50:19', 57, '2014-03-13 19:50:19', NULL, 0, 1),
(14, '39977e2fc750241c804994397cf8b76c', 8, 'Other Current Liabilities', '2014-03-13 19:50:38', 57, '2014-10-17 13:55:45', 1, 0, 1),
(15, '7b1da30decc220313554a3e0b5ab09a0', 6, 'Account payables', '2014-03-13 19:50:53', 57, '2014-03-13 19:50:53', NULL, 0, 1),
(16, 'c1dfa9cd93afa0abd860326855797e29', 5, 'Other asset', '2014-03-13 19:51:10', 57, '2014-03-13 19:51:10', NULL, 0, 1),
(17, 'e398d469bbc2f368e1e7a0e2c21b7fb8', 1, 'Cash on hand', '2014-03-13 19:51:39', 57, '2014-03-13 19:51:39', NULL, 0, 1),
(18, '31e441193d85126ab089c7bca84c1d89', 8, 'Current liaiblities', '2014-03-15 17:39:43', 57, '2014-03-15 17:39:43', NULL, 0, 1),
(19, '1f78fc6491f921fcc418a9268640b187', 3, 'Inventory', '2014-10-17 08:48:52', 1, '2014-10-17 08:48:52', NULL, 0, 1),
(20, '6cc66793cc2d806bcad05ec909b0c453', 10, 'Equity', '2014-10-17 09:49:21', 1, '2014-10-17 09:49:21', NULL, 0, 1),
(21, '7f5ff8802e6e258e6446ea2c6b59fe20', 11, 'Sale Discounts', '2014-10-17 09:57:35', 1, '2014-10-17 09:57:35', NULL, 0, 1),
(22, '0968b70433446f9cc072e49209ce010c', 13, 'Utilities Expense', '2014-10-17 13:32:56', 1, '2014-10-17 13:32:56', NULL, 0, 1),
(23, 'd70f12336f882f383a969d363395f132', 13, 'Repairs Expense', '2014-10-17 13:38:54', 1, '2014-10-17 13:38:54', NULL, 0, 1),
(24, 'be758512053a57750430e7885acc2889', 13, 'Purchase Discounts', '2014-10-17 13:44:00', 1, '2014-10-17 13:44:00', NULL, 0, 1),
(25, 'd36a4e1bf11b920eb1e5d701b712680d', 2, 'Notes Receivable', '2014-10-17 13:50:52', 1, '2014-10-17 13:50:52', NULL, 0, 1),
(26, '3ee9d414fc1b1230fc0c330890d801d1', 3, 'Prepaid Expense', '2015-05-04 16:56:24', 1, '2015-05-04 16:56:24', NULL, 0, 1);

--
-- Triggers `chart_account_groups`
--
DROP TRIGGER IF EXISTS `zChartAccountGroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zChartAccountGroupBfInsert` BEFORE INSERT ON `chart_account_groups`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.chart_account_type_id = "" OR NEW.chart_account_type_id = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chart_account_types`
--

CREATE TABLE IF NOT EXISTS `chart_account_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Dumping data for table `chart_account_types`
--

INSERT INTO `chart_account_types` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Cash and Bank', '2011-12-29 13:48:14', 1, NULL, NULL, 1),
(2, 'Accounts Receivable', '2011-12-29 13:48:33', 1, NULL, NULL, 1),
(3, 'Other Current Asset', '2011-12-29 13:48:51', 1, NULL, NULL, 1),
(4, 'Fixed Asset', '2011-12-29 13:49:00', 1, NULL, NULL, 1),
(5, 'Other Asset', '2011-12-29 13:49:19', 1, NULL, NULL, 1),
(6, 'Accounts Payable', '2011-12-29 13:49:25', 1, NULL, NULL, 1),
(7, 'Credit Card', '2011-12-29 13:49:32', 1, NULL, NULL, 1),
(8, 'Other Current Liability', '2011-12-29 13:49:41', 1, NULL, NULL, 1),
(9, 'Long Term Liability', '2011-12-29 13:49:51', 1, NULL, NULL, 1),
(10, 'Equity', '2011-12-29 13:49:55', 1, NULL, NULL, 1),
(11, 'Income', '2011-12-29 13:49:59', 1, NULL, NULL, 1),
(12, 'Cost of Goods Sold', '2011-12-29 13:50:07', 1, NULL, NULL, 1),
(13, 'Expense', '2011-12-29 13:50:16', 1, NULL, NULL, 1),
(14, 'Other Income', '2011-12-29 13:50:21', 1, NULL, NULL, 1),
(15, 'Other Expense', '2011-12-29 13:50:26', 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chief_complains`
--

CREATE TABLE IF NOT EXISTS `chief_complains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `ordering` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `sys_code`, `parent_id`, `name`, `description`, `ordering`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'd5487e87fa6c71af1c9758a4737bdfec', NULL, 'Class', '', NULL, '2017-07-21 11:23:24', 1, '2017-07-21 11:23:24', NULL, 1);

--
-- Triggers `classes`
--
DROP TRIGGER IF EXISTS `zClassBfInsert`;
DELIMITER //
CREATE TRIGGER `zClassBfInsert` BEFORE INSERT ON `classes`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `class_companies`
--

CREATE TABLE IF NOT EXISTS `class_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `class_companies`
--

INSERT INTO `class_companies` (`id`, `class_id`, `company_id`) VALUES
(1, 1, 1);

--
-- Triggers `class_companies`
--
DROP TRIGGER IF EXISTS `zClassCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zClassCompanyBfInsert` BEFORE INSERT ON `class_companies`
 FOR EACH ROW BEGIN
	IF NEW.class_id = "" OR NEW.class_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE IF NOT EXISTS `codes` (
  `iphash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`iphash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`iphash`, `code`, `created`) VALUES
('29ed1c649f41d36716de23dfc8f532c8', 'tufted', 1550739873),
('63e557c242ca3ea6be8a32bca0da86a9', 'upgrew', 1539749957),
('ea6443f3cb01794dabaa30ae847698ae', 'hombre', 1539751223),
('eeaf3ac643e18a0254c3543fd226bf8a', 'cabbie', 1539752494);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `duplicate` (`name`),
  KEY `name` (`name`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment_category_results`
--

CREATE TABLE IF NOT EXISTS `comment_category_results` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `communes`
--

CREATE TABLE IF NOT EXISTS `communes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`),
  KEY `district_id` (`district_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=173 ;

--
-- Dumping data for table `communes`
--

INSERT INTO `communes` (`id`, `sys_code`, `district_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(2, '0bfa766e36fe3ae852dee48f24ebe10e', 3, 'TONLE BASSAK', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(3, '706cce60d604d3d53dd2006f0670ae5c', 3, 'BOEUNG KENG KANG I', '2014-12-22 14:57:34', 1, '2015-03-14 12:33:09', 2, 1),
(4, 'ae8937c954b110918868158b561f2708', 3, 'BOEUNG KENG KANG II', '2014-12-22 14:57:34', 1, '2015-03-14 12:33:17', 2, 1),
(5, 'af028cebd630f31f1b8a7e22094da8e2', 3, 'BOEUNG KENG KANG III', '2014-12-22 14:57:34', 1, '2015-03-14 12:33:32', 2, 1),
(6, 'b4758e17b43a0229b04c96d4cb356ebf', 3, 'BOEUNG TRABEK', '2014-12-22 14:57:34', 1, '2015-03-14 12:34:09', 2, 1),
(7, '0a1d8e1a91652fd7e877727fa7d663af', 3, 'TUMNUP TUEK', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(8, 'edab8f24a5c0a95d9090693e54c6717d', 3, 'PHSAR DOEUM THKOV', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(9, 'ad993440cdb41f3bbaaa77df8f73e8f8', 3, 'TOUL SVAY PREY I', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(10, 'b6cddb44cb91e8004f6a7e3f1b6b45b1', 3, 'TOUL SVAY PREY II', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(11, 'b3751b59b0fdf61b08573f97525bfaae', 3, 'TOUL TUM POUNG I', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(12, '63d9e3731850ef57d95a1c35ca8803b4', 3, 'TOUL TUM POUNG II', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(13, '1cf36bab67da44ccb0408987ad757177', 3, 'OLYMPIC', '2014-12-22 14:57:34', 1, '2014-12-22 14:57:34', NULL, 1),
(14, '6b3bdb285c8596e9e57d0342f407e8fa', 6, 'DANGKAO', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(15, '548f1409cc539c5ca6e5411f3c6c9774', 6, 'TRAPANG KRASANG', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(16, '6a5081dcd5ed1309bacb3fcc3859e5f1', 6, 'KORKROKA', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(17, '90df5d23a1dc00b402d0f3ab5a5ff1b4', 6, 'PHLEUNG CHHESROTES', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(18, '789ca042dc460d362c38d391d9811df5', 13, 'CHAOM CHAO', '2014-12-22 15:01:26', 1, '2014-12-25 09:04:24', 1, 2),
(19, '854d0228fcd8f22330537ee262df6c19', 6, 'PORNG TUEK', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(20, 'ad0f4b7e9fe564aef864eb55b7287626', 6, 'PREY VENG', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(21, 'ecfd0347edf1065a7c59d3b7d972b0c4', 6, 'SAMRONG', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(22, 'f7c8c041896faf866cb902fbbaf5e994', 6, 'PREY SAR', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(23, '50f6fa9f488fcd0909a5b49318d20980', 6, 'KRAING THNONG', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(24, '2ffd8d48aa9a4c78480b01cda88572d2', 6, 'KRAING PONGRO', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(25, 'ac2b498971289236ff0c2942c8019ab3', 6, 'PRATASLANG', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(26, '78671df564b947ad6c529efacf1473f2', 6, 'SAC SAMPEOU', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(27, '1054c5e89b84213ae87d666ac8c9b25e', 6, 'CHHEUNG EK', '2014-12-22 15:01:26', 1, '2014-12-22 15:01:26', NULL, 1),
(28, '1395712015884d424bb3338debcbd1b7', 7, 'SRAAS CHAK', '2014-12-22 15:03:45', 1, '2014-12-23 08:41:20', 1, 1),
(29, '6fdd4f8f0b50233034b39cf6717a7981', 7, 'WAT PHNOM', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(30, '6813d90afc4d1f963281e945a2724b41', 7, 'PHSAR CHAS', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(31, 'b83b82f805b18e3ef6bb88c5f13e806f', 7, 'PHSAR KANDAL I', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(32, '35053fa087cda8f6c12149224831a432', 7, 'PHSAR KANDAL II', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(33, '596c586ca24462ca2172aeaa0eea7e19', 7, 'CHEY CHOMNAS', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(34, '9cf17d9ef3f7976ec0c17394f64e91f3', 7, 'CHAKTOMUK', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(35, 'a193da28f7bdcf558b1658ff1ff38e86', 7, 'PHSAR THMEY I', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(36, '31e154d43cb147b1e9040ff17815f77b', 7, 'PHSAR THMEY II', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(37, '04e001abac16c4a16c1a5e2341047034', 7, 'PHSAR THMEY III', '2014-12-22 15:03:45', 1, '2014-12-22 15:03:45', NULL, 1),
(38, 'cf0a7457c501382ab871d18c5e471573', 7, 'BOEUNG RAING', '2014-12-22 15:03:45', 1, '2015-03-14 12:33:50', 2, 1),
(39, '8476d84032c6cc7400d24ecac66f0c38', 8, 'BOEUNG TUMPUN', '2014-12-22 15:05:26', 1, '2015-03-14 12:34:17', 2, 1),
(40, '1d5354718d9e6a6be59613440550a169', 8, 'STUNG MEANCHEY', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(41, '567d2c2a370be7b4049ddbfc411756d1', 8, 'CHAK ANGRE KROM', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(42, 'ab96220fa79a5cc9f1c791328ef23a84', 8, 'CHAK ANGRE LEUR', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(43, 'dda88a3e1c9f0ce88c15c099022bcdf1', 8, 'CHBAR AMPOV I', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(44, 'fd6da8642ee0e7fcc4a6b9dab78210dc', 8, 'CHBAR AMPOV II', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(45, 'b3251fd609dbd419dd761f483a13c989', 8, 'NIROTH', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 2),
(46, 'd45bcc01d945e9d1b4113f4050662898', 8, 'PREK PRA', '2014-12-22 15:05:26', 1, '2014-12-22 15:05:26', NULL, 1),
(47, 'aff71f22da5f2d33279a7cb9f6d10369', 9, 'MONOROM', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(48, '18d064754fd3b2b87a28bec7e51514be', 9, 'MITTAPHEAP', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(49, 'a25a3935819b7ef3df2a482470d8e616', 9, 'VEAL VONG', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(50, '779a71f9bc19bac6f82253cc17ec1811', 9, 'ORUSSEY I', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(51, 'a41caac158bf278d4acdd0e2853684ba', 9, 'ORUSSEY II', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(52, '64042161d869dcec8fee7ecf9be233d2', 9, 'ORUSSEY III', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(53, 'b69a7cfdffbf4b184c35783359ce43f0', 9, 'ORUSSEY IV', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(54, '0108efde10a5807eff2284ff2a90e16e', 9, 'ORUSSEY V', '2014-12-22 15:06:45', 1, '2014-12-22 15:06:45', NULL, 1),
(55, 'e582744a940b9f11ca41573e1d4d1f15', 11, 'PHNOM PENH THMEY', '2014-12-22 15:07:51', 1, '2014-12-22 15:07:51', NULL, 1),
(56, '139d0dee63e2ee3b767534214cd3ccfb', 11, 'TUEK THLA', '2014-12-22 15:07:51', 1, '2014-12-22 15:07:51', NULL, 1),
(57, '46cfae998492bf975bee31a4280321a8', 11, 'KHMOUNH', '2014-12-22 15:07:51', 1, '2014-12-22 15:07:51', NULL, 1),
(58, '931861fad92796bc6d5fdcf6206d3607', 12, 'BOEUNG KAK I', '2014-12-22 15:09:41', 1, '2015-03-14 12:32:53', 2, 1),
(59, 'aba5dab101d7bedca8efcee02f1e5be5', 12, 'BOEUNG KAK II', '2014-12-22 15:09:41', 1, '2015-03-14 12:33:01', 2, 1),
(60, '144aa674075587be5b93a3d76eec42e7', 12, 'PHSAR DEPO I', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(61, '0f95b116975a3f4811abf05ffdc15ef6', 12, 'PHSAR DEPO II', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(62, '51caa040cdae627694c9ae479f5ab5b1', 12, 'PHSAR DEPO III', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(63, '61486bc116fd9ac2ec80c0e3e58fb43e', 12, 'TUEK LAAK I', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(64, 'ca16c3eba07a9cdc8d49915afbda8a6e', 12, 'TUEK LAAK II', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(65, '166954d5b691f480b5dd901ed547107c', 12, 'TUEK LAAK III', '2014-12-22 15:09:41', 1, '2014-12-22 15:09:41', NULL, 1),
(66, '4d8d04f9052fe8b8f077bb2db56d0929', 12, 'PHSAR DOEUM KOR', '2014-12-22 15:09:41', 1, '2015-03-07 17:01:44', 2, 1),
(67, '598ff5736d16fb410ae565293510c869', 12, 'BOEUNG SALANG', '2014-12-22 15:09:41', 1, '2015-03-14 12:34:02', 2, 1),
(68, 'ce862af8cd219d50a5d552b3029d2df1', 10, 'SVAY PAK', '2014-12-22 15:14:06', 1, '2014-12-22 15:14:06', NULL, 1),
(69, '7b9cb826f2688fae624504a0612985bc', 10, 'KILO 6', '2014-12-22 15:14:06', 1, '2014-12-22 15:14:06', NULL, 1),
(70, '9fe3ebc84931f66599d8c11e1ec55925', 15, 'BANTEAY NEANG', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(71, 'b12a2c228069d2bbd73246233e184e72', 15, 'BAT TRANG', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(72, '4f39a95a58d7887951dea388d28c9f94', 15, 'CHAMNAOM', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(73, 'a6508fd272d5801abefff4174639b1a3', 15, 'KOUK BALANG', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(74, '1ac18ee7c95b9b4e09c5fe6c7e8ca753', 15, 'KOY MAENG', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(75, '648306766aac788c2a03757ff92ed48c', 15, 'O PRASAT', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(76, '5ce261c28a181a132c73e3d1b832a57d', 15, 'PHNOM TOUCH', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(77, '62cd3571ab2431578f2d1666288908a1', 15, 'ROHAT TUEK', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(78, '38396e6bb347b52d63ff66021ef8fdc8', 15, 'RUSSEI KRAOK', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(79, 'ade158be1ada80eae439e1deaed37440', 15, 'SAMBUOR', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(80, '530577d9bbb4a129e248c1e64e44d9c8', 15, 'SRAAS REANG', '2014-12-22 15:17:21', 1, '2014-12-23 08:41:39', 1, 1),
(81, 'fa319d6e31c547e4c97a01a712815a49', 15, 'TA LAM', '2014-12-22 15:17:21', 1, '2014-12-22 15:17:21', NULL, 1),
(82, '787db5bc5df38ac531321da88baefe6f', 16, 'NAM TAU', '2014-12-22 15:19:23', 1, '2014-12-22 15:19:23', NULL, 1),
(83, 'e3a4e093e1da2d894c10dff983b0693a', 16, 'PAOY CHAR', '2014-12-22 15:19:23', 1, '2014-12-22 15:19:23', NULL, 1),
(84, 'acadb8c8b0834188b355963d603fb497', 16, 'PHNOM DEY', '2014-12-22 15:19:23', 1, '2014-12-22 15:19:23', NULL, 1),
(85, 'b8a30610224a52202e38b868aac49ec2', 16, 'PONLEY', '2014-12-22 15:19:23', 1, '2014-12-22 15:19:23', NULL, 1),
(86, 'e9e41aaedbcbdd9f7fe6c456bcd2f768', 16, 'SPEAN SRENG ROUK', '2014-12-22 15:19:23', 1, '2014-12-22 15:19:23', NULL, 1),
(87, 'c4c8ef06a91bfba24e14cf056cbdb39c', 16, 'SRAAS CHIK', '2014-12-22 15:19:23', 1, '2014-12-23 08:41:29', 1, 1),
(88, '4824d9ee8790803a978245644d4d0958', 17, 'CHHNUOR', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(89, 'e94dde2264d424d7b286e85c31f8e220', 17, 'CHOB', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(90, '7553873edf3ca51e49a36574325740a5', 17, 'PHNOM LIEB', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(91, '17cc57e0ef6c5952537fb45a072115f8', 17, 'PRASAT CHAR', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(92, '5294da4bedeef12aec7f0765b9946208', 17, 'PREAH NET', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(93, '1225d1ebda85d9cddb61068b235cf43f', 17, 'ROHAL ROHAL', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(94, '6c3ec98a5145bdc6a244c47eac5cd919', 17, 'TEAN KAM', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(95, 'dcd3b0d7a5b17bbc5f7b098f76edd3cf', 17, 'TUEK CHOUR SMACH', '2014-12-22 15:21:21', 1, '2014-12-22 15:21:21', NULL, 1),
(96, '9490a6ee3a8e1a913acf4bdcf873330f', 18, 'CHANGHA', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(97, '8293190427907c4e2a2a5a9fdedc5d7e', 18, 'KOUB', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(98, '1b194726f4e6c4470756570eda1cdb3c', 18, 'KUTTASAT', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(99, 'dbb1d425d64ca15a92b7736325dc2501', 18, 'NIMITT', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(100, 'f66f445ba62d6ce96e6c09692e1d4835', 18, 'O BEI CHOAN', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(101, 'fed5a2ee7fc1c5e2dabbc6a395d90d64', 18, 'PAOY PAET', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(102, '95c035ea6534f7733126a244be9176a4', 18, 'SAMRAONG', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(103, 'b4d43c3bf219cb7b7eb40189bd499a4b', 18, 'SOENGH', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(104, 'c75087a0f15fb8e6fca006ca22c9189b', 18, 'SOUPHI', '2014-12-22 15:24:00', 1, '2014-12-22 15:24:00', NULL, 1),
(105, 'fc8af04c1c1b6a39843b377587026e1f', 19, 'BOS SBOV', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(106, '2b8c3792707bab00ccadb20964a7abde', 19, 'KAMPONG SVAY', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(107, '0f3f7f1e79f47825d3c8b3ab4c01c215', 19, 'KOH PORNG SATV', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(108, 'c326eec0b3455f66dbaca31cbafed281', 19, 'MKAK', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(109, 'b65aa43218920087d1a46f78c017b131', 19, 'O AMBEL', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(110, '75eaaa178c5e89e37fe83192d72c508d', 19, 'PHNIET', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(111, 'b15c4e06cd7a68bcbfa265759ea32d25', 19, 'PREAH PONLEA', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(112, '448da17f2936247060a3d9e28979048c', 19, 'TUEK THLA', '2014-12-22 15:29:31', 1, '2014-12-22 15:29:31', NULL, 1),
(113, '195356a26b71a7a344db7cb267feb02d', 21, 'PHKOAM', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(114, '2ec7cb990832d0547293a8f51efcc7b4', 21, 'ROLUOS', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(115, 'b3acb616868f8c9f36c8c93461701646', 21, 'SARONGK', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(116, '14780fa9e3554426abe18f0c72cdf5a7', 21, 'SLA KRAM', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(117, '69ec4cc9ab9e8f0711cb81eb861386c3', 21, 'SVAY CHEK', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(118, '7edb5aac554c0b72ba11e22bf05f6dc4', 21, 'TA BAEN', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(119, 'baa86f7138aae59238fbb1e350ce96cd', 21, 'TA PHOU', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(120, 'ec4c7ae29a7fc8b0cda0e917067f939b', 21, 'TREAS', '2014-12-22 16:15:52', 1, '2014-12-22 16:15:52', NULL, 1),
(121, 'e94f369197741909643fa6d271e91842', 20, 'BANTEAY CHMAR', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(122, 'e11aab33312452e8cf4ebeff020184d2', 20, 'KOK KAKTHEN', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(123, 'd0c545db4bf909279dafbac64f5c1e22', 20, 'KOK ROMIET', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(124, '561d851bf577179be4aa5c0603e8c098', 20, 'KUMRU', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(125, '821c631ba1ba5c5231951db59116b2b9', 20, 'PHNOM THMEY', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(126, 'c8c3a5a804bb9c3ebe1c9581adb095de', 20, 'THMAR POUK', '2014-12-22 16:17:14', 1, '2014-12-22 16:17:14', NULL, 1),
(127, '9670f1b2d0715417d1fc1128a0a7fc7b', 22, 'BOEUNG BENG', '2014-12-22 16:18:10', 1, '2015-03-14 12:32:45', 2, 1),
(128, 'bb9b5e5fc46730747381465bbc6e67f7', 22, 'MALAI', '2014-12-22 16:18:10', 1, '2014-12-22 16:18:10', NULL, 1),
(129, '2c4f7be52ba3137e1d6f18d8d979c049', 22, 'O SRALAU', '2014-12-22 16:18:10', 1, '2014-12-22 16:18:10', NULL, 1),
(130, '597d9bc56677cf5764f61f7e2f2ac55f', 22, 'TA KONG', '2014-12-22 16:18:10', 1, '2014-12-22 16:18:10', NULL, 1),
(131, '827be6cdc985df7377de9d8825388687', 22, 'TOUL PONGRO', '2014-12-22 16:18:10', 1, '2014-12-22 16:18:10', NULL, 1),
(132, '6bc8fdec5c3f5384536b50e63e6bbfb8', 4, 'NIROTH', '2014-12-23 08:53:24', 1, '2014-12-23 08:53:24', NULL, 1),
(133, 'a7ca8a1a432bbc91330769156894592c', 13, 'CHOAM CHOA', '2014-12-24 13:35:03', 1, '2014-12-24 13:35:03', NULL, 1),
(134, '66cbdbb8e2fe201250d464e04d9a3d81', 89, 'PREK SOMROUNG', '2015-01-08 17:49:05', 2, '2015-01-08 17:49:05', NULL, 1),
(135, '45c40f13284843db5a2ba908a5f87199', 10, 'TOUL SANGKE', '2015-01-08 17:53:06', 2, '2015-01-08 17:53:06', NULL, 1),
(136, '6d36634a4ede900590b3b26f7409d7af', 13, 'KAKAB', '2015-01-08 17:57:59', 2, '2015-01-08 17:57:59', NULL, 1),
(137, '03d074372da12bf62b947c68000ed4e5', 25, 'SVAY PAO', '2015-01-09 10:55:48', 2, '2015-01-09 10:55:48', NULL, 1),
(138, 'd94e3d73c559e3e5e8109a99fa1082d4', 40, 'BOEUNG KOK', '2015-01-10 13:41:55', 2, '2015-03-14 12:33:42', 2, 1),
(139, 'a3c8e20ec97c8918a2dc949daca6c1c4', 163, 'SVAY DANGKUM', '2015-01-10 16:44:28', 2, '2015-01-10 16:44:28', NULL, 1),
(140, '867d11f247da39f6500a45ce6df57d88', 47, 'PONLAI', '2015-01-15 11:54:59', 2, '2015-01-15 11:54:59', NULL, 1),
(141, '90b9f927a35fe2c22c37ecb4c4c577b9', 49, 'KAMPONG CHHNANG', '2015-01-15 11:56:36', 2, '2015-01-15 11:56:36', NULL, 1),
(142, 'ba3b4bff020a440bc9bd923da6eb8f80', 52, 'PONGRO', '2015-01-15 11:57:11', 2, '2015-01-15 11:57:11', NULL, 1),
(143, '54b7bff5678cc2afefe48bcfebeddda5', 52, 'ROLEA BIER', '2015-01-15 11:57:38', 2, '2015-01-15 11:57:38', NULL, 1),
(144, 'e9aa0581a6ec8a20d27f852271061fcc', 45, 'PREK POR', '2015-01-15 12:14:39', 2, '2015-01-15 12:14:39', NULL, 1),
(145, 'ce51a59cbb8f16e778e6433dc504da26', 45, 'PREK DAMBOK', '2015-01-15 12:15:16', 2, '2015-01-15 12:15:16', NULL, 1),
(146, '361e763af930b96a87886167249237ce', 59, 'VEANG CHAS', '2015-01-15 12:18:26', 2, '2015-01-15 12:18:26', NULL, 1),
(147, 'c0f3f0369af16d5de6607ce64fbd7564', 80, 'KOR KI', '2015-01-19 09:57:16', 2, '2015-01-19 09:57:16', NULL, 1),
(148, '75be1a066c8d9136d88d1b3725acf5a3', 51, 'ORRUSEY', '2015-01-19 09:58:06', 2, '2015-01-19 09:58:06', NULL, 1),
(149, '300d144193955927771c3a8404ccefe5', 85, 'ROR KAKORNG', '2015-01-19 10:01:43', 2, '2015-01-19 10:01:43', NULL, 1),
(150, 'd7ba325c7ed9a8182adfff6f9b164071', 87, 'KAMPONG LEUNG', '2015-01-19 10:03:31', 2, '2015-01-19 10:03:31', NULL, 1),
(151, 'd431d7876c9102a5cd6c3e9ea9c9615b', 87, 'PREK SVAY', '2015-01-19 10:16:36', 2, '2015-01-19 10:16:36', NULL, 2),
(152, '9dae705ac5d8d84b562f86fe91d11ef0', 140, 'PREK SVAY', '2015-01-19 10:17:41', 2, '2015-01-19 10:17:41', NULL, 1),
(153, 'c0c785a1d10ff2a12c3b835aa396900e', 178, 'SVAY RIENG', '2015-01-19 10:19:24', 2, '2015-01-19 10:19:24', NULL, 1),
(154, '6dad8f792fbe0f040f4b26cbf42e50ab', 183, 'TUM LAOB', '2015-01-19 10:30:53', 2, '2015-01-19 10:30:53', NULL, 1),
(155, 'a2a60f89dd2f8e6af6599a6621ff97e7', 86, 'ANG SNOUL', '2015-01-19 10:31:53', 2, '2015-01-19 10:31:53', NULL, 1),
(156, '6a17ed6b98b57f02a4705a0df3942b86', 86, 'BEK CHAAN', '2015-01-19 10:33:01', 2, '2015-01-19 10:33:01', NULL, 1),
(157, 'be7ef7a151ab88ad2f68077625f2bcbd', 78, 'KAMPOT', '2015-01-19 10:33:24', 2, '2015-01-19 10:33:24', NULL, 1),
(158, '3de414996eae76f061c6ad46c95b613f', 200, 'KAMPONG SPEU', '2015-01-24 15:35:15', 2, '2015-01-24 15:35:15', NULL, 1),
(159, '3ffb0d387ae755dc3f16d35bcc7b112b', 188, 'TRAM KNOR', '2015-01-24 15:36:00', 2, '2015-01-24 15:36:00', NULL, 1),
(160, 'fab5508d9c794095e45ef54c9607b988', 83, 'PREK TUMLOAP', '2015-01-26 11:34:03', 2, '2015-01-26 11:34:03', NULL, 1),
(161, 'ccc10bc1762e4e0bbd24affd26d796c2', 173, 'PREY ANGKUNGH', '2015-01-26 11:35:24', 2, '2015-01-26 11:35:24', NULL, 1),
(162, 'f88b1a2eef138f3e3faa95c43ea26ca1', 81, 'PREK TAMAB', '2015-01-26 11:44:15', 2, '2015-01-26 11:51:39', 2, 1),
(163, 'c0b678b533909be0e8e3ef131da37173', 10, 'CHRANG CHAMRESH', '2015-01-29 11:13:53', 2, '2015-01-29 11:13:53', NULL, 1),
(164, 'a671e036b458e168e018155615814dcb', 201, 'PREAH SIHANOUK', '2015-01-30 17:05:53', 2, '2015-01-30 17:05:53', NULL, 1),
(165, '708f266bcb9879c358e574f9618dac4c', 94, 'KOH KONG', '2015-02-09 11:02:45', 2, '2015-02-09 11:02:45', NULL, 1),
(166, 'd50ea411285029e9f5126688fa21ae65', 52, 'ANDOUNG SNAY', '2015-02-24 10:10:17', 2, '2015-02-24 10:10:17', NULL, 1),
(167, '30312daf059605de037363a98cb9ae91', 59, 'VONG CHAAS', '2015-02-24 10:12:25', 2, '2015-02-24 10:12:25', NULL, 1),
(168, 'cbf14d233f2a5117738ce2d254bb75e8', 85, 'PREK ANCHANH', '2015-02-24 10:13:09', 2, '2015-02-24 10:13:09', NULL, 1),
(169, 'bb53e29a6918bb8eaeccbd0f6d0e6083', 141, 'ORKA', '2015-02-24 10:21:04', 2, '2015-02-24 10:21:04', NULL, 2),
(170, '6e656438d86ca0e7b74eedeb11ec5596', 141, 'ROKA', '2015-02-24 10:21:43', 2, '2015-02-24 10:21:43', NULL, 1),
(171, 'abf9bbc1dcee1cfad0b52110a1a3f2f4', 138, 'CHI PHOEH', '2015-02-24 10:23:15', 2, '2015-02-24 10:23:15', NULL, 1),
(172, 'de7af5345cd2340755bbb93d750b49d6', 202, 'SANGKAT MONOROM', '2015-03-23 14:56:10', 2, '2015-03-23 14:56:10', NULL, 1);

--
-- Triggers `communes`
--
DROP TRIGGER IF EXISTS `zCommunesBfInsert`;
DELIMITER //
CREATE TRIGGER `zCommunesBfInsert` BEFORE INSERT ON `communes`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.district_id = "" OR NEW.district_id = NULL THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_other` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `vat_number` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vat_calculate` tinyint(4) unsigned DEFAULT NULL COMMENT '1: Before Discount, Make Up; 2: After Discount, Make Up',
  `website` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `classes` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `currency_center_id` (`currency_center_id`),
  KEY `searchs` (`name`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `sys_code`, `name`, `name_other`, `currency_center_id`, `vat_number`, `vat_calculate`, `website`, `photo`, `classes`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'e75a9336d36e2a42a6d3e1bb48a1daec', 'Sunshine Kids Clinic', 'គ្លីនិកកុមារ​ សាន់សាញ', 1, 'K-0000000001', 1, '', 'b2b5dfccd2e29039be6f95eedccc6b04.png', 'a:1:{i:1;a:2:{i:1;s:1:"1";i:2;s:1:"1";}}', '', '2017-07-21 10:06:21', 1, '2023-06-07 11:53:07', 1, 1);

--
-- Triggers `companies`
--
DROP TRIGGER IF EXISTS `zCompanyAfterInsert`;
DELIMITER //
CREATE TRIGGER `zCompanyAfterInsert` AFTER INSERT ON `companies`
 FOR EACH ROW BEGIN
	INSERT INTO customer_companies (customer_id, company_id) VALUES (1, NEW.id);
	INSERT INTO chart_account_companies (chart_account_id, company_id) SELECT id, NEW.id FROM chart_accounts WHERE id >= 1 AND id <= 104;
	INSERT INTO price_type_companies (price_type_id, company_id) VALUES (1, NEW.id);
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zCompanyBeforeInsert`;
DELIMITER //
CREATE TRIGGER `zCompanyBeforeInsert` BEFORE INSERT ON `companies`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.photo = "" OR NEW.photo = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_other = "" OR NEW.name_other = NULL OR NEW.currency_center_id = "" OR NEW.currency_center_id = NULL OR NEW.vat_calculate = "" OR NEW.vat_calculate = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zCompanyBfUpdate`;
DELIMITER //
CREATE TRIGGER `zCompanyBfUpdate` BEFORE UPDATE ON `companies`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.photo = "" OR NEW.photo = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_other = "" OR NEW.name_other = NULL OR NEW.currency_center_id = "" OR NEW.currency_center_id = NULL OR NEW.vat_calculate = "" OR NEW.vat_calculate = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `company_categories`
--

CREATE TABLE IF NOT EXISTS `company_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(255)),
  KEY `other_name` (`other_name`(255)),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Dumping data for table `company_categories`
--

INSERT INTO `company_categories` (`id`, `sys_code`, `name`, `other_name`, `created`, `is_active`) VALUES
(1, '6dc85d00efe67b7a5e071a64af1dc633', 'Arts, crafts, and collectibles', NULL, '2017-01-26 16:28:21', 1),
(2, '04c13dd1c13b02c188ddf7ee90246913', 'Baby', NULL, '2017-01-26 16:28:21', 1),
(3, 'e38ecd6e1e7a8bdb102f785fa6492f58', 'Beauty and fragrances', NULL, '2017-01-26 16:28:21', 1),
(4, 'b0dabedd849626b7b7f4491cb28a593e', 'Books and magazines', NULL, '2017-01-26 16:28:21', 1),
(5, '76a36ab302f74a113e90748941371049', 'Business to business', NULL, '2017-01-26 16:28:21', 1),
(6, '1bd8204a5ddb1196490a01b7ae11bc5f', 'Clothing, accessories, and shoes ', NULL, '2017-01-26 16:28:21', 1),
(7, '07a0a620f401776293e9928be32b25fc', 'Computers, accessories, and services', NULL, '2017-01-26 16:28:21', 1),
(8, '041aa86cc576b4b19a1b1fe81f64a25d', 'Education', NULL, '2017-01-26 16:28:21', 1),
(9, 'c11905b0bf5826c9d19bebd04bb38e32', 'Electronics and telecom', NULL, '2017-01-26 16:28:21', 1),
(10, 'f5c930fe7de24681bad21e8151406bfd', 'Entertainment and media', NULL, '2017-01-26 16:28:21', 1),
(11, '20d9ee466b981e69391f32057eb19b32', 'Financial services and products', NULL, '2017-01-26 16:28:21', 1),
(12, '8ee8b9787c08e7086a5f5c153d11be24', 'Food retail and service', NULL, '2017-01-26 16:28:21', 1),
(13, '3c883792631e90df8dfdc666d55845b6', 'Gifts and flowers', NULL, '2017-01-26 16:28:21', 1),
(14, '304f94deb6ce17e21da59e1a9312f54a', 'Health and personal care', NULL, '2017-01-26 16:28:21', 1),
(15, 'be381fe9fe4af42f444f77fd9bc1803e', 'Home and garden', NULL, '2017-01-26 16:28:21', 1),
(16, 'da7191330b9777075f2b240921312d63', 'Pets and animals', NULL, '2017-01-26 16:28:21', 1),
(17, '5599f3642d4c7a882cd324016d09dcbe', 'Services - other', NULL, '2017-01-26 16:28:21', 1),
(18, '11cbcc024c05dbbf3e0992fd830e6d89', 'Sports and outdoors', NULL, '2017-01-26 16:28:21', 1),
(19, '7da3459c73669497df2338da4f21891a', 'Toys and hobbies ', NULL, '2017-01-26 16:28:21', 1),
(20, '28d196a4afe4752e453a6e4620dd366a', 'Vehicle sales', NULL, '2017-01-26 16:28:21', 1),
(21, 'a6697970730c8db47813b47eba0ad2f0', 'Vehicle service and accessories', NULL, '2017-01-26 16:28:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_insurances`
--

CREATE TABLE IF NOT EXISTS `company_insurances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_insurance_id` int(11) DEFAULT NULL,
  `insurance_code` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `company_insurances`
--

INSERT INTO `company_insurances` (`id`, `group_insurance_id`, `insurance_code`, `name`, `business_number`, `personal_number`, `other_number`, `fax_number`, `email_address`, `address`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 1, '19IN0000001', 'A', '09332866', '', '', '', 'A@gmail.com', 'PP', '2019-05-03 16:45:44', 1, '2019-05-03 16:46:09', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_insurance_companies`
--

CREATE TABLE IF NOT EXISTS `company_insurance_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_insurance_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`company_insurance_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `company_insurance_companies`
--

INSERT INTO `company_insurance_companies` (`id`, `company_insurance_id`, `company_id`) VALUES
(2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_with_categories`
--

CREATE TABLE IF NOT EXISTS `company_with_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `company_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `searchs` (`company_id`,`company_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=45 ;

--
-- Dumping data for table `company_with_categories`
--

INSERT INTO `company_with_categories` (`id`, `company_id`, `company_category_id`) VALUES
(43, 1, 2),
(44, 1, 14);

--
-- Triggers `company_with_categories`
--
DROP TRIGGER IF EXISTS `zCompanyCategoryBfInsert`;
DELIMITER //
CREATE TRIGGER `zCompanyCategoryBfInsert` BEFORE INSERT ON `company_with_categories`
 FOR EACH ROW BEGIN
	IF NEW.company_id = "" OR NEW.company_id = NULL OR NEW.company_category_id = "" OR NEW.company_category_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `consignments`
--

CREATE TABLE IF NOT EXISTS `consignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_contact_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_group_to_id` int(11) DEFAULT NULL,
  `sales_rep_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1: Edit; 0: Void; 1: Issued; 2: Fullfiled',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filters` (`date`,`code`,`status`),
  KEY `filter2` (`customer_id`,`location_group_id`,`location_group_to_id`,`sales_rep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_details`
--

CREATE TABLE IF NOT EXISTS `consignment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consignment_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(15,2) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `consignment_id` (`consignment_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_receives`
--

CREATE TABLE IF NOT EXISTS `consignment_receives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consignment_id` int(11) DEFAULT NULL,
  `consignment_detail_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `total_qty` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_id` (`consignment_id`),
  KEY `filters` (`location_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_returns`
--

CREATE TABLE IF NOT EXISTS `consignment_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_contact_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_group_to_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1: Edit; 0: Void; 1: Issued; 2: Fullfiled',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filters` (`date`,`code`,`status`),
  KEY `filter2` (`customer_id`,`location_group_id`,`location_group_to_id`,`consignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_return_details`
--

CREATE TABLE IF NOT EXISTS `consignment_return_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consignment_return_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(15,2) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `consignment_return_id` (`consignment_return_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_return_receives`
--

CREATE TABLE IF NOT EXISTS `consignment_return_receives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consignment_return_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `total_qty` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_return_id` (`consignment_return_id`),
  KEY `filters` (`location_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `consignment_term_conditions`
--

CREATE TABLE IF NOT EXISTS `consignment_term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consignment_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_search` (`consignment_id`,`term_condition_type_id`,`term_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=233 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `is_active`) VALUES
(1, 'Afghanistan', 1),
(2, 'Albania', 1),
(3, 'Algeria', 1),
(4, 'American Samoa', 1),
(5, 'Andorra', 1),
(6, 'Angola', 1),
(7, 'Anguilla', 1),
(8, 'Antarctica', 1),
(9, 'Antigua & barbuda', 1),
(10, 'Argentina', 1),
(11, 'Armenia', 1),
(12, 'Aruba', 1),
(13, 'Australia', 1),
(14, 'Austria', 1),
(15, 'Azerbaijan', 1),
(16, 'Bahamas', 1),
(17, 'Bahrain', 1),
(18, 'Bangladesh', 1),
(19, 'Barbados', 1),
(20, 'Belarus', 1),
(21, 'Belgium', 1),
(22, 'Belize', 1),
(23, 'Benin', 1),
(24, 'Bermuda', 1),
(25, 'Bhutan', 1),
(26, 'Bolivia', 1),
(27, 'Bosnia herzegovina', 1),
(28, 'Botswana', 1),
(29, 'Bouvet Island', 1),
(30, 'Brazil', 1),
(31, 'Brunei Darussalam', 1),
(32, 'Bulgaria', 1),
(33, 'Burkinafaso', 1),
(34, 'Burma', 1),
(35, 'Burundi', 1),
(36, 'Cambodia', 1),
(37, 'Cameroon', 1),
(38, 'Canada', 1),
(39, 'Cape Verde', 1),
(40, 'Cayman Islands', 1),
(41, 'Central african rep', 1),
(42, 'Chad', 1),
(43, 'Chile', 1),
(44, 'China', 1),
(45, 'Christmas Island', 1),
(46, 'Colombia', 1),
(47, 'Comoros', 1),
(48, 'Congo', 1),
(49, 'Cook Islands', 1),
(50, 'Costa Rica', 1),
(51, 'Cote D''Ivoire', 1),
(52, 'Croatia', 1),
(53, 'Cuba', 1),
(54, 'Cyprus', 1),
(55, 'Czech Republic', 1),
(56, 'Demrepcongo', 1),
(57, 'Denmark', 1),
(58, 'Djibouti', 1),
(59, 'Dominica', 1),
(60, 'East Timor', 1),
(61, 'Ecuador', 1),
(62, 'Egypt', 1),
(63, 'El Salvador', 1),
(64, 'Equatorial Guinea', 1),
(65, 'Eritrea', 1),
(66, 'Estonia', 1),
(67, 'Ethiopia', 1),
(68, 'Faroe Islands', 1),
(69, 'Fiji', 1),
(70, 'Finland', 1),
(71, 'France', 1),
(72, 'France, Metropolitan', 1),
(73, 'French Guiana', 1),
(74, 'French Polynesia', 1),
(75, 'Gabon', 1),
(76, 'Gambia', 1),
(77, 'Georgia', 1),
(78, 'Germany', 1),
(79, 'Ghana', 1),
(80, 'Gibraltar', 1),
(81, 'Greece', 1),
(82, 'Greenland', 1),
(83, 'Grenada', 1),
(84, 'Grenadines', 1),
(85, 'Guadeloupe', 1),
(86, 'Guam', 1),
(87, 'Guatemala', 1),
(88, 'Guinea', 1),
(89, 'Guinea-bissau', 1),
(90, 'Guyana', 1),
(91, 'Haiti', 1),
(92, 'Honduras', 1),
(93, 'Hong Kong', 1),
(94, 'Hungary', 1),
(95, 'Iceland', 1),
(96, 'India', 1),
(97, 'Indonesia', 1),
(98, 'Iran', 1),
(99, 'Iraq', 1),
(100, 'Ireland', 1),
(101, 'Israel', 1),
(102, 'Italy', 1),
(103, 'Ivory Coast', 1),
(104, 'Jamaica', 1),
(105, 'Japan', 1),
(106, 'Jordan', 1),
(107, 'Kazakhstan', 1),
(108, 'Kenya', 1),
(109, 'Kiribati', 1),
(110, 'Kuwait', 1),
(111, 'Kyrgyzstan', 1),
(112, 'Laos', 1),
(113, 'Latvia', 1),
(114, 'Lebanon', 1),
(115, 'Lesotho', 1),
(116, 'Liberia', 1),
(117, 'Libya', 1),
(118, 'Liechtenstein', 1),
(119, 'Lithuania', 1),
(120, 'Luxembourg', 1),
(121, 'Macadonia', 1),
(122, 'Macau', 1),
(123, 'Madagascar', 1),
(124, 'Malawi', 1),
(125, 'Malaysia', 1),
(126, 'Maldives', 1),
(127, 'Mali', 1),
(128, 'Malta', 1),
(129, 'Marshall Islands', 1),
(130, 'Martinique', 1),
(131, 'Mauritania', 1),
(132, 'Mauritius', 1),
(133, 'Mayotte', 1),
(134, 'Mexico', 1),
(135, 'Micronesia', 1),
(136, 'Moldova', 1),
(137, 'Monaco', 1),
(138, 'Mongolia', 1),
(139, 'Montserrat', 1),
(140, 'Morocco', 1),
(141, 'Mozambique', 1),
(142, 'Myanmar', 1),
(143, 'Namibia', 1),
(144, 'Nauru', 1),
(145, 'Nepal', 1),
(146, 'Neth Antilles', 1),
(147, 'Netherlands', 1),
(148, 'New Caledonia', 1),
(149, 'New Zealand', 1),
(150, 'Nicaragua', 1),
(151, 'Niger', 1),
(152, 'Nigeria', 1),
(153, 'Niue', 1),
(154, 'Norfolk Island', 1),
(155, 'North Korea', 1),
(156, 'Norway', 1),
(157, 'Oman', 1),
(158, 'Pakistan', 1),
(159, 'Palau', 1),
(160, 'Panama', 1),
(161, 'Papua Newguinea', 1),
(162, 'Paraguay', 1),
(163, 'Peru', 1),
(164, 'Philippines', 1),
(165, 'Pitcairn', 1),
(166, 'Poland', 1),
(167, 'Portugal', 1),
(168, 'Puerto Rico', 1),
(169, 'Qatar', 1),
(170, 'Rawanda', 1),
(171, 'Râ”œÂ®publique dâ”œÂ®mocratique du Congo', 1),
(172, 'Reunion', 1),
(173, 'Romania', 1),
(174, 'Russian Federation', 1),
(175, 'Saint Kitts and Nevis', 1),
(176, 'Saint Lucia', 1),
(177, 'Samoa', 1),
(178, 'San Marino', 1),
(179, 'Sao Tome', 1),
(180, 'Saudi Arabia', 1),
(181, 'Senegal', 1),
(182, 'Serbia', 1),
(183, 'Seychelles', 1),
(184, 'Sierra Leone', 1),
(185, 'Singapore', 1),
(186, 'Slovakia', 1),
(187, 'Slovenia', 1),
(188, 'Solomon Islands', 1),
(189, 'Somalia', 1),
(190, 'South Africa', 1),
(191, 'South Korea', 1),
(192, 'Spain', 1),
(193, 'Sri Lanka', 1),
(194, 'St. Helena', 1),
(195, 'Stkitts Nevis', 1),
(196, 'Sudan', 1),
(197, 'Suriname', 1),
(198, 'Swaziland', 1),
(199, 'Sweden', 1),
(200, 'Switzerland', 1),
(201, 'Syria', 1),
(202, 'Taiwan', 1),
(203, 'Tajikistan', 1),
(204, 'Tanzania', 1),
(205, 'Thailand', 1),
(206, 'Togo', 1),
(207, 'Tokelau', 1),
(208, 'Tonga', 1),
(209, 'Trinidad & Tobago', 1),
(210, 'Tunisia', 1),
(211, 'Turkey', 1),
(212, 'Turkmenistan', 1),
(213, 'Tuvala', 1),
(214, 'Uganda', 1),
(215, 'Ukraine', 1),
(216, 'United Arab Emerates', 1),
(217, 'United Kingdom', 1),
(218, 'United States', 1),
(219, 'Uruguay', 1),
(220, 'Ussr', 1),
(221, 'Uzbekistan', 1),
(222, 'Vanuatu', 1),
(223, 'Venezuela', 1),
(224, 'Viet Nam', 1),
(225, 'Virgin Islands (British)', 1),
(226, 'Virgin Islands (U.S.)', 1),
(227, 'Western Sahara', 1),
(228, 'Yemen', 1),
(229, 'Yugoslavia', 1),
(230, 'Zaire', 1),
(231, 'Zambia', 1),
(232, 'Zimbabwe', 1);

-- --------------------------------------------------------

--
-- Table structure for table `credit_memos`
--

CREATE TABLE IF NOT EXISTS `credit_memos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `invoice_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `ar_id` int(11) DEFAULT NULL,
  `cm_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reason_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `total_amount_invoice` decimal(15,3) DEFAULT NULL,
  `balance` decimal(15,3) DEFAULT NULL,
  `discount` decimal(15,3) DEFAULT NULL,
  `discount_percent` decimal(6,3) DEFAULT NULL,
  `mark_up` decimal(15,3) DEFAULT NULL,
  `vat_chart_account_id` int(11) DEFAULT NULL,
  `total_vat` decimal(15,3) DEFAULT NULL,
  `vat_percent` decimal(5,3) DEFAULT NULL,
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_filter_second` (`sales_order_id`,`ar_id`,`reason_id`,`price_type_id`),
  KEY `key_search` (`invoice_code`,`invoice_date`,`cm_code`,`balance`,`order_date`,`created_by`,`status`),
  KEY `key_filter` (`location_group_id`,`location_id`,`company_id`,`customer_id`,`currency_center_id`,`vat_chart_account_id`,`vat_setting_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `credit_memos`
--
DROP TRIGGER IF EXISTS `zCreditMemoAfInsert`;
DELIMITER //
CREATE TRIGGER `zCreditMemoAfInsert` AFTER INSERT ON `credit_memos`
 FOR EACH ROW BEGIN
	DECLARE salesMonth int(11);
	DECLARE salesYear int(11);
	SET salesMonth = MONTH(NEW.order_date);
	SET salesYear  = YEAR(NEW.order_date);
	IF NEW.status = 2 THEN
		INSERT INTO report_sales_by_days (`date`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `c_total_amount`, `c_total_discount`, `c_total_mark_up`, `c_total_vat`) 
		VALUES (NEW.order_date, NEW.company_id, NEW.branch_id, NEW.customer_id, 0, NEW.total_amount, NEW.discount, NEW.mark_up, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE c_total_amount = c_total_amount + NEW.total_amount, c_total_discount = c_total_discount + NEW.discount, c_total_mark_up = c_total_mark_up + NEW.mark_up, c_total_vat = c_total_vat + NEW.total_vat;
		INSERT INTO report_sales_by_months (`month`, `year`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `c_total_amount`, `c_total_discount`, `c_total_mark_up`, `c_total_vat`) 
		VALUES (salesMonth, salesYear, NEW.company_id, NEW.branch_id, NEW.customer_id, 0, NEW.total_amount, NEW.discount, NEW.mark_up, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE c_total_amount = c_total_amount + NEW.total_amount, c_total_discount = c_total_discount + NEW.discount, c_total_mark_up = c_total_mark_up + NEW.mark_up, c_total_vat = c_total_vat + NEW.total_vat;
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zCreditMemoAfUpdate`;
DELIMITER //
CREATE TRIGGER `zCreditMemoAfUpdate` AFTER UPDATE ON `credit_memos`
 FOR EACH ROW BEGIN
	DECLARE salesMonth int(11);
	DECLARE salesYear int(11);
	SET salesMonth = MONTH(OLD.order_date);
	SET salesYear  = YEAR(OLD.order_date);
	IF (OLD.status = 2 AND NEW.status = -1) OR (OLD.status = 2 AND NEW.status = 0) THEN
		UPDATE report_sales_by_days SET c_total_amount = c_total_amount - OLD.total_amount, c_total_discount = c_total_discount - OLD.discount, c_total_mark_up = c_total_mark_up - OLD.mark_up, c_total_vat = c_total_vat - OLD.total_vat WHERE date = OLD.order_date AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = 0;
		UPDATE report_sales_by_months SET c_total_amount = c_total_amount - OLD.total_amount, c_total_discount = c_total_discount - OLD.discount, c_total_mark_up = c_total_mark_up - OLD.mark_up, c_total_vat = c_total_vat - OLD.total_vat WHERE month = salesMonth AND year = salesYear AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = 0;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_details`
--

CREATE TABLE IF NOT EXISTS `credit_memo_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit_memo_id` int(11) unsigned DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_memo_id` (`credit_memo_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_miscs`
--

CREATE TABLE IF NOT EXISTS `credit_memo_miscs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit_memo_id` int(11) unsigned DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(10) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_memo_id` (`credit_memo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_receipts`
--

CREATE TABLE IF NOT EXISTS `credit_memo_receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_memo_id` int(11) unsigned DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `receipt_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_us` decimal(15,3) DEFAULT '0.000',
  `amount_other` decimal(15,3) DEFAULT '0.000',
  `total_amount` decimal(15,3) DEFAULT '0.000',
  `balance` decimal(15,3) DEFAULT '0.000',
  `balance_other` decimal(15,3) DEFAULT '0.000',
  `change` decimal(15,3) DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_void` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `credit_memo_id` (`credit_memo_id`),
  KEY `receipt_code` (`receipt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_services`
--

CREATE TABLE IF NOT EXISTS `credit_memo_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit_memo_id` int(10) unsigned DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_memo_id` (`credit_memo_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_with_sales`
--

CREATE TABLE IF NOT EXISTS `credit_memo_with_sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_memo_id` int(10) unsigned DEFAULT NULL,
  `sales_order_id` int(10) unsigned DEFAULT NULL,
  `total_price` decimal(15,3) DEFAULT '0.000',
  `status` tinyint(4) DEFAULT NULL,
  `apply_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_memo_id` (`credit_memo_id`),
  KEY `sales_order_id` (`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `crontab_inv_adjs`
--

CREATE TABLE IF NOT EXISTS `crontab_inv_adjs` (
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  UNIQUE KEY `cycle_product_id` (`cycle_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency_centers`
--

CREATE TABLE IF NOT EXISTS `currency_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `symbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_searchs` (`name`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `currency_centers`
--

INSERT INTO `currency_centers` (`id`, `sys_code`, `name`, `symbol`, `photo`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'c3691f6f110a04bc03f49ff1b6be11c2', 'USD - US Dollar', '$', NULL, '2015-12-16 13:43:33', 1, '2015-12-16 13:43:33', NULL, 1),
(2, '50dbcae7b49c7ba293200a3df51b23eb', 'KHR - Cambodia Riel', '៛', NULL, '2015-12-16 13:44:48', 1, '2015-12-16 13:44:48', NULL, 1),
(3, '5fed42699043f9baba7e518941dafa90', 'THB - Thai Baht', '฿', NULL, '2015-12-16 13:45:56', 1, '2015-12-16 13:45:56', NULL, 1),
(4, '74305edde4cd955d64ab68229714e719', 'VND - Viet Nam Dong', '₫', NULL, '2015-12-16 13:47:36', 1, '2015-12-16 13:47:36', NULL, 1),
(5, 'abea1ec3126d23d3eda701ecc0a1dcd7', 'MYR - Malaysia Ringgit', 'RM', NULL, '2015-12-16 13:48:12', 1, '2015-12-16 13:48:12', NULL, 1),
(6, 'bb5cfa9f1bfad3cb7a7a749870181e6e', 'LAK - Laos Kip', '₭', NULL, '2015-12-16 13:49:50', 1, '2015-12-16 13:51:55', NULL, 1),
(7, '1571d082a6c3362c83785d28a56856f8', 'SGD - Singapore Dollar', '$', NULL, '2015-12-16 13:51:00', 1, '2015-12-16 13:52:42', NULL, 1),
(8, '9b50c6bfb5aec95e1d10ed7b6a5398a3', 'PHP - Philippines Peso', '₱', NULL, '2015-12-16 13:51:40', 1, '2015-12-16 13:51:40', NULL, 1),
(9, '2926eeaaaeaa0873afcee0f6480e56b4', 'IDR - Indonesia Rupiah', 'Rp', NULL, '2015-12-16 13:53:30', 1, '2015-12-16 13:53:30', NULL, 1),
(10, '983db8739b2fdf6ebffbff1b750a8e8c', 'BND - Brunei Darussalam Dollar', '$', NULL, '2015-12-16 13:54:40', 1, '2015-12-16 13:54:40', NULL, 1),
(11, '5e41b3512e59058fc1d631133f3df8f8', 'CNY - China Yuan Renminbi', '¥', NULL, '2015-12-17 15:11:01', 1, '2015-12-17 15:11:01', NULL, 1),
(12, '0b81d08dc7383cef91a806761cca7f72', 'JPY - Japan Yen', '¥', NULL, '2015-12-17 15:13:56', 1, '2015-12-17 15:13:56', NULL, 1),
(13, '18db72eada1a4375c5a916de6d7ecb5c', 'KRW - Korea (South) Won', '₩', NULL, '2015-12-17 15:15:31', 1, '2015-12-17 15:15:31', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `house_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `address` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_term_id` int(11) DEFAULT NULL,
  `payment_every` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_kh` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `limit_balance` double DEFAULT NULL,
  `limit_total_invoice` int(11) DEFAULT NULL,
  `vat` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `total_sales` int(11) DEFAULT NULL,
  `last_invoice_cm` int(11) DEFAULT NULL,
  `last_invoice_cm_date` date DEFAULT NULL COMMENT 'Invoice Has CM or Not',
  `last_invoice_cm_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_invoice` int(11) DEFAULT NULL COMMENT 'Invoice Not Has CM',
  `last_invoice_date` date DEFAULT NULL,
  `last_invoice_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_cm` int(11) DEFAULT NULL,
  `last_cm` int(11) DEFAULT NULL,
  `last_cm_date` date DEFAULT NULL,
  `last_cm_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_order` int(11) DEFAULT NULL,
  `last_order` int(11) DEFAULT NULL,
  `last_order_date` date DEFAULT NULL,
  `last_order_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_quote` int(11) DEFAULT NULL,
  `last_quote` int(11) DEFAULT NULL,
  `last_quote_date` date DEFAULT NULL,
  `last_quote_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '1: Country, 2: Over Sea',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `is_active` (`is_active`),
  KEY `searchs` (`customer_code`,`name`,`name_kh`,`main_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `customers`
--
DROP TRIGGER IF EXISTS `zCustomerBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zCustomerBeforeDelete` BEFORE DELETE ON `customers`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete general customer';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zCustomerBeforeUpdate`;
DELIMITER //
CREATE TRIGGER `zCustomerBeforeUpdate` BEFORE UPDATE ON `customers`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot edit general customer';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zCustomerBfInsert`;
DELIMITER //
CREATE TRIGGER `zCustomerBfInsert` BEFORE INSERT ON `customers`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_kh = "" OR NEW.name_kh = NULL OR NEW.payment_term_id = "" OR NEW.payment_term_id = NULL OR NEW.main_number = "" OR NEW.main_number = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_cgroups`
--

CREATE TABLE IF NOT EXISTS `customer_cgroups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `cgroup_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `cgroup_id` (`cgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `customer_cgroups`
--
DROP TRIGGER IF EXISTS `zCustomerCgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zCustomerCgroupBfInsert` BEFORE INSERT ON `customer_cgroups`
 FOR EACH ROW BEGIN
	IF NEW.customer_id = "" OR NEW.customer_id = NULL OR NEW.cgroup_id = "" OR NEW.cgroup_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_companies`
--

CREATE TABLE IF NOT EXISTS `customer_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `customer_companies`
--
DROP TRIGGER IF EXISTS `zCustomerCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zCustomerCompanyBfInsert` BEFORE INSERT ON `customer_companies`
 FOR EACH ROW BEGIN
	IF NEW.customer_id = "" OR NEW.customer_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_contacts`
--

CREATE TABLE IF NOT EXISTS `customer_contacts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `title` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_telephone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `modules` (`company_id`,`customer_id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`contact_name`,`is_active`,`contact_telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `customer_contacts`
--
DROP TRIGGER IF EXISTS `zCustomerContactBfInsert`;
DELIMITER //
CREATE TRIGGER `zCustomerContactBfInsert` BEFORE INSERT ON `customer_contacts`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.customer_id = "" OR NEW.customer_id = NULL OR NEW.contact_name = "" OR NEW.contact_name = NULL OR NEW.sex = "" OR NEW.sex = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cycle_products`
--

CREATE TABLE IF NOT EXISTS `cycle_products` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `location_id` (`location_group_id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cycle_product_details`
--

CREATE TABLE IF NOT EXISTS `cycle_product_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `lots_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `current_qty` int(11) DEFAULT '0',
  `new_qty` int(11) DEFAULT '0',
  `qty_difference` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cycle_product_id` (`cycle_product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cystoscopy_services`
--

CREATE TABLE IF NOT EXISTS `cystoscopy_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cystoscopy_service_request_id` int(11) NOT NULL,
  `cystoscopy_service_queue_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conclusion` text COLLATE utf8_unicode_ci,
  `descript_before_sdate` text COLLATE utf8_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `urethra_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urethra` text COLLATE utf8_unicode_ci,
  `prostate_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prostate` text COLLATE utf8_unicode_ci,
  `bladder_neck_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bladder_neck` text COLLATE utf8_unicode_ci,
  `bladder_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bladder` text COLLATE utf8_unicode_ci,
  `after_five_minute_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `after_five_minute` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cystoscopy_service_requests`
--

CREATE TABLE IF NOT EXISTS `cystoscopy_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `cystoscopy_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cystoscopy_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `cystoscopy_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `cystoscopy_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cystoscopy_service_request_updates`
--

INSERT INTO `cystoscopy_service_request_updates` (`id`, `other_service_request_id`, `cystoscopy_description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 1, 'ds', '2023-02-21 13:38:41', 1, '2023-02-21 13:38:41', NULL, 1),
(2, 1, 'ds', '2023-02-21 14:10:06', 1, '2023-02-21 14:10:06', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `daily_clinical_reports`
--

CREATE TABLE IF NOT EXISTS `daily_clinical_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `daily_clinical_reports`
--

INSERT INTO `daily_clinical_reports` (`id`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Treatment', '+ Treatment\n1/ Infusion D1/2S, 500ml rate 100ml/h.\n2/ Ceftriaxone 1g iv + 100ml NSS rate 200ml/h.\n3/ Paracetamol kabi 20ml rate 100ml/h.', '2019-04-19 09:10:58', 1, '2019-04-19 09:10:58', NULL, 1),
(2, 'Daily report', '- Good general condition.\n- Good consciousness.\n- Fever up and down.\n- Poor/ Good feeding.\n- Normal defecation.\n- Normal urine output.\n- No respiratory distress.\n- Good hemodynamic status.', '2019-04-19 09:14:33', 1, '2019-04-19 09:14:33', NULL, 1),
(3, 'A', 'A', '2019-05-03 16:22:21', 1, '2019-05-03 16:22:21', NULL, 1),
(4, 'Pharyngitis', '1/ Paracetamol 50ml IV for 15mn.\n2/ Ceftriaxone 1g in 100ml NSS rate 150ml/h.', '2019-07-22 09:05:13', 1, '2019-07-22 09:05:13', NULL, 1),
(5, 'Clinic of Dengue fever ', '- Good general condition.\n- High fever.\n- Pules : 80/mn.\n- BP : 110/70mmHg.\n- Good urine output.', '2019-07-22 09:09:13', 1, '2019-07-22 09:09:13', NULL, 1),
(6, 'Acute bronchiolitis', '- He looks tired.\n- Mild fever.\n- Mild dyspnea.\n- Coughing alot.\n- Secretion alot.\n- Poor feeding.\n- Sibilant rales at both lungs.', '2019-07-22 09:12:39', 1, '2019-07-22 09:12:39', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_payable`
--

CREATE TABLE IF NOT EXISTS `dashboard_payable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `amount` decimal(20,9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filter` (`purchase_order_id`,`chart_account_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_profit_loss`
--

CREATE TABLE IF NOT EXISTS `dashboard_profit_loss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `debit` decimal(20,9) DEFAULT NULL,
  `credit` decimal(20,9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `chart_account_id` (`chart_account_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_receivable`
--

CREATE TABLE IF NOT EXISTS `dashboard_receivable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `amount` decimal(20,9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filter` (`sales_order_id`,`chart_account_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `branch_id` int(10) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `ship_to` text COLLATE utf8_unicode_ci,
  `customer_contact_id` int(11) DEFAULT NULL,
  `delivery_by` int(11) DEFAULT NULL COMMENT 'Employee',
  `long` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lat` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_receive` tinyint(4) DEFAULT NULL COMMENT '0: Not Yet; 1: Received',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `picked` datetime DEFAULT NULL,
  `picked_by` int(10) DEFAULT NULL,
  `delivered` datetime DEFAULT NULL,
  `delivered_by` int(10) DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  `closed_by` int(10) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0: Void; 1: Issue; 2: Picked; 3: Delivered; 4: Completed',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `search` (`date`,`code`,`status`),
  KEY `delivery_by` (`delivery_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_details`
--

CREATE TABLE IF NOT EXISTS `delivery_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` int(11) DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `sales_order_detail_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL COMMENT 'Total By Invoice',
  `total_pick` int(11) DEFAULT NULL COMMENT 'Total Pick',
  `aisle` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bay` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_id` (`delivery_id`),
  KEY `location_id` (`location_id`),
  KEY `product_id` (`product_id`),
  KEY `sales_order_detail_id` (`sales_order_detail_id`),
  KEY `locationInfo` (`aisle`,`position`,`bay`,`level`,`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `sys_code`, `company_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, NULL, 1, 'OPD ', '2016-07-20 10:52:15', 1, '2016-07-20 10:52:15', NULL, 1),
(2, NULL, 1, 'Critical Care ICU (REA)', '2016-07-20 10:53:14', 1, '2016-07-20 10:57:00', 1, 1),
(3, NULL, 1, 'Surgery', '2016-07-20 10:53:25', 1, '2016-07-20 10:53:25', NULL, 1),
(4, NULL, 1, 'OB/GYN', '2016-07-20 10:53:39', 1, '2016-07-20 10:53:39', NULL, 1),
(5, NULL, 1, 'Nursery', '2016-07-20 10:53:57', 1, '2016-07-20 10:56:44', 1, 1),
(6, NULL, 1, 'MICU', '2016-07-20 10:54:07', 1, '2016-07-20 10:54:07', NULL, 1),
(7, NULL, 1, 'IPD', '2016-07-20 10:54:35', 1, '2016-07-20 10:54:35', NULL, 1),
(8, NULL, 1, 'Emergency', '2016-07-20 10:54:49', 1, '2016-07-20 10:54:49', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `diagnostics`
--

CREATE TABLE IF NOT EXISTS `diagnostics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `diagnostics`
--

INSERT INTO `diagnostics` (`id`, `type`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(4, NULL, 'BPH', 'Hello World Hello World Hello World \n\nHello World Hello World Hello World', '2018-09-05 16:14:54', 1, '2019-02-18 13:41:50', 1, 0),
(5, NULL, 'Common Cold', 'Common Cold', '2019-02-18 13:44:56', 1, '2019-02-18 13:44:56', NULL, 1),
(6, NULL, 'Diarrhea', 'Diarrhea', '2019-02-18 13:45:40', 1, '2019-02-18 13:51:20', 1, 0),
(7, NULL, 'Acute Pharyngitis', 'Acute Pharyngitis', '2019-02-18 13:46:02', 1, '2019-02-18 13:46:02', NULL, 1),
(8, NULL, 'Constipation', 'Constipation', '2019-02-18 13:46:30', 1, '2019-02-18 13:46:30', NULL, 1),
(9, NULL, 'Skin Allergic', 'Skin Allergic', '2019-02-18 13:47:45', 1, '2019-02-18 13:47:45', NULL, 1),
(10, NULL, 'Asthma', 'Asthma', '2019-02-18 13:48:02', 1, '2019-02-18 13:48:02', NULL, 1),
(11, NULL, 'Acute Laryngitis', 'Acute Laryngitis', '2019-02-18 13:48:25', 1, '2019-02-18 13:48:25', NULL, 1),
(12, NULL, 'Acute Conjunctivitis', 'Acute Conjunctivitis', '2019-02-18 13:50:00', 1, '2019-02-18 13:50:00', NULL, 1),
(13, NULL, 'Food Poisoning', 'Food Poisoning', '2019-02-18 13:51:05', 1, '2019-02-18 13:51:05', NULL, 1),
(14, NULL, 'Acute Diarrhea', 'Acute Diarrhea', '2019-02-18 13:51:46', 1, '2019-02-18 13:51:46', NULL, 1),
(15, NULL, 'Allergic Rhinitis', 'Allergic Rhinitis', '2019-02-18 13:52:46', 1, '2019-02-18 13:52:46', NULL, 1),
(16, NULL, 'Hand Food and Mouth Disease', 'Hand Food and Mouth Disease', '2019-02-18 13:53:29', 1, '2019-02-18 13:53:29', NULL, 1),
(17, NULL, 'Immunization', 'Immunization', '2019-02-18 13:54:09', 1, '2019-02-18 13:54:09', NULL, 1),
(18, NULL, 'Acute Bronchitis : Good general condition. Mild fever. Coughing with mild difficult to breath. Pharynx no imflammed. Some bronchial rales at the right lung. No any others abnormalities found in physical examination.', 'Acute Bronchitis : Good general condition. Mild fever. Coughing with mild difficult to breath. Pharynx no imflammed. Some bronchial rales at the right lung. No any others abnormalities found in physical examination.', '2019-02-18 14:21:02', 1, '2019-02-18 14:23:01', 1, 0),
(19, NULL, 'Dengue hemorragic fever', 'Dengue hemorragic fever', '2019-02-18 14:22:19', 1, '2019-02-18 14:22:43', 1, 0),
(20, NULL, 'Acute Bronchitis', 'Acute Bronchitis', '2019-02-18 14:23:22', 1, '2019-05-03 16:21:39', 1, 1),
(21, NULL, 'Dengue hemorragic fever', 'Dengue hemorragic fever', '2019-02-18 14:24:03', 1, '2019-02-18 14:24:03', NULL, 1),
(22, NULL, 'A', 'A', '2019-05-03 16:21:43', 1, '2019-05-03 16:21:43', NULL, 1),
(23, NULL, 'Acute gastro-enteritis', 'Acute gastro-enteritis', '2019-10-26 09:26:54', 1, '2019-10-26 09:26:54', NULL, 1),
(24, NULL, 'Acute bronchiolitis', 'Acute bronchiolitis', '2020-01-19 13:00:46', 1, '2020-01-19 13:00:46', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `income_chart_account_id` int(10) DEFAULT NULL,
  `expense_chart_account_id` int(10) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `percent` decimal(5,3) DEFAULT NULL,
  `amount` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `company_id` (`company_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `discounts`
--
DROP TRIGGER IF EXISTS `zDiscountBfInsert`;
DELIMITER //
CREATE TRIGGER `zDiscountBfInsert` BEFORE INSERT ON `discounts`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.name = "" OR NEW.name = NULL OR (NEW.percent = "" AND NEW.amount = "") THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE IF NOT EXISTS `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`),
  KEY `province_id` (`province_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=203 ;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `sys_code`, `province_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(3, '308706047a8b7c78623813e9fc2351ff', 26, 'CHAMKARMON', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(4, 'f2aa763acf42fa8496e3c2c563dd7fe7', 26, 'CHBAR AMPOV', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(5, 'fe4da99e594b211dfe412ca103633133', 26, 'CHROY CHANGVA', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(6, '604cc04a7366dc7ce06513624bef23e6', 26, 'DANGKAO', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(7, 'ea1d9ede0eb5d94c0c3c75a8bbe1eb37', 26, 'DAUN PENH', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(8, '7dea419bf5bfd956dae331f53426dcb4', 26, 'MEANCHEY', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(9, '1e7287f8f58cba8871b1d60f9bf52f4f', 26, '7 MAKARA', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(10, 'd689ca2e0dc2db06d64e5148f1df294d', 26, 'RUSSEY KEO', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(11, '5cff26afeb16f14da0f4ce2bf2c24d7a', 26, 'SEN SOK', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(12, '5fd3e3e3540c13f0e8658e06a9c15d02', 26, 'TOUL KORK', '2014-12-03 11:40:16', 1, '2014-12-22 14:15:20', 1, 1),
(13, '68f38b9f6a64ee0c5061c5e0cce5d5ce', 26, 'PORSENCHEY', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(14, '7ead0754421e743e862c9850946afe2d', 26, 'PREK PNOV', '2014-12-03 11:40:16', 1, '2014-12-03 11:40:16', NULL, 1),
(15, 'f8a043dc9ddbe5ccbc671c79a10a98c5', 27, 'MONGKOL BOREAY', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(16, '8329e3f84a80a370d4d6dd2861736d59', 27, 'PHNOM SROK', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(17, '9db2601442e26fdae6e872ce9ccad5fe', 27, 'PREAH NET PREAH', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(18, '82de72321908f72d47173a52b07d3deb', 27, 'O CHROV', '2014-12-22 13:18:42', 1, '2014-12-22 15:22:21', 1, 1),
(19, 'a179081598432dc42e6e2e2d274d1236', 27, 'SEREI SAOPHOAN', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(20, '538d4784bbb2931efaf6c2da4a7ab9b6', 27, 'THMAR POUK', '2014-12-22 13:18:42', 1, '2014-12-22 14:16:01', 1, 1),
(21, '00a11e1fc151b4e6a5f052e9abdd710e', 27, 'SVAY CHEK', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(22, 'ccc8f8cd772c67d1b6e8afc31dc91777', 27, 'MALAI', '2014-12-22 13:18:42', 1, '2014-12-22 13:18:42', NULL, 1),
(23, 'dbf75a33036e461a0efc0e44bcf77a04', 28, 'BANAN', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(24, '7af62e472cfbaa8699c71505aa777e3e', 28, 'THMAR KOUL', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(25, '668ec33f8f985613d9714721f626a262', 28, 'BATTAMBANG', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(26, '116ddca51d786f363b25e395f8721fac', 28, 'BAVEL', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(27, '65293ef3b86dd72c95919b77b080da85', 28, 'EK PHNOM', '2014-12-22 13:20:05', 1, '2014-12-22 14:16:34', 1, 1),
(28, 'd0bc3b562a44492c0b24424c50603625', 28, 'MOUNG RUSSEI', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(29, '6268b62e252d3158443bdc6ea70a9a2a', 28, 'RATANAK MONDUL', '2014-12-22 13:20:05', 1, '2014-12-22 13:20:05', NULL, 1),
(30, '047e04d4eba23c085e16b9f3726f4dbb', 28, 'SANGKE', '2014-12-22 13:22:54', 1, '2014-12-22 14:14:16', 1, 1),
(31, '6e2f81d508c596bbb1870623f5be5fb0', 28, 'SAMLOUT', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(32, '16b8cf69a023d4218edd8465163bff4e', 28, 'SAMPOV LOUN', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(33, '3b01ed59a18d666ee1bdbc8d2387f9b6', 28, 'PHNOM PROEK', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(34, '339d6fa1815337341ba7866769471f80', 28, 'KAMRIENG', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(35, '34d93bf124da5fa0b98e6a588e3b5ede', 28, 'KOAS KRALA', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(36, '2404fe6d6c5debe3769516309bf2ccec', 28, 'RUHAKIRI', '2014-12-22 13:22:54', 1, '2014-12-22 13:22:54', NULL, 1),
(37, '51a73f9739e8f21708382dbdc2802b4f', 29, 'BATHEAY', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(38, '17cae5ada33e9ecc38baa7abe7fb0940', 29, 'CHAMKAR LEU', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(39, '80dc9246fac0077781dea1b21b30e852', 29, 'CHEUNG PREY', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(40, 'b143281f04ad356220529e07e66c95b8', 29, 'KAMPONG CHAM', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(41, 'd5442bc96d3b07c65a3bebfa30d9125f', 29, 'KAMPONG SIEM', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(42, '55854c2bdb675a3ae7d92d34c24be1b9', 29, 'KANG MEAS', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(43, '552b77e67bb7a060ee15b9b991040197', 29, 'KOH SOTIN', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(44, '44e4bc9b04babe418789874970da12a0', 29, 'PREY CHHOR', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(45, 'a180e7e4aaaab179800588fd6b235f3c', 29, 'SREY SANTHOR', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(46, 'e1c982d723fb385d4504be568f17b225', 29, 'STUNG TRANG', '2014-12-22 13:25:17', 1, '2014-12-22 13:25:17', NULL, 1),
(47, '2cefe4a97b42fbb0781231a09fb91c20', 30, 'BARIBOUR', '2014-12-22 13:25:36', 1, '2014-12-22 13:25:36', NULL, 1),
(48, 'bb4044cb8a0eecbaec5202b0812adc83', 29, 'CHOL KIRI', '2014-12-22 13:27:51', 1, '2014-12-22 13:27:51', NULL, 1),
(49, '1fda6f2d3765505a1d4a7412697eae22', 30, 'KAMPONG CHHNANG', '2014-12-22 13:27:51', 1, '2015-01-15 11:56:09', 2, 1),
(50, 'ec46a2935a39cf8b0b01be5f247a4395', 29, 'KAMPONG LENG', '2014-12-22 13:27:51', 1, '2014-12-22 13:27:51', NULL, 1),
(51, 'fa771c3b4f21a98561e347a1ab1e10dd', 30, 'KAMPONG TRALACH', '2014-12-22 13:27:51', 1, '2015-01-19 09:59:48', 2, 1),
(52, '7a431de0963cb6bd97e79270d840c43a', 30, 'ROLEA BIER', '2014-12-22 13:27:51', 1, '2015-01-15 11:50:10', 2, 1),
(53, '3404a7082331fe1935f654df030d8f20', 29, 'SAMAKI MEANCHEY', '2014-12-22 13:27:51', 1, '2014-12-22 13:27:51', NULL, 1),
(54, 'bccd3e08903f3fa1a5648ccd3099a94f', 29, 'TUEK PHOS', '2014-12-22 13:27:51', 1, '2014-12-22 13:27:51', NULL, 1),
(55, '86bbbd647ec23513805410677d4c9b51', 31, 'BASET', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(56, '5faee10ff4fa5c4944e8d32e6280a3b7', 31, 'CHBAR MON', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(57, '3a6e8070f6d2755c0536134636ce2b23', 31, 'KONG PISEI', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(58, 'fd3e3cc506aa787797b87f8b5181f89c', 31, 'AURAL', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(59, 'f7c56a11dc26246eead76270f249a728', 31, 'UDONG', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(60, '4108f95d2a759340f2ed892829b7c40b', 31, 'PHNOM SRUOCH', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(61, '0353d254829d9662f20917fbe4be0685', 31, 'SAMRAONG TONG', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(62, '2cf3c6584c301d6e9fb93d43697971be', 31, 'THPONG', '2014-12-22 13:29:23', 1, '2014-12-22 13:29:23', NULL, 1),
(63, '90d2cdfb3118e2510f3de792346b98be', 32, 'BARAY', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(64, '875d5aca4b0d9c09565ed35a46c261ec', 32, 'KAMPONG SVAY', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(65, '768342bde9c68daf3468430cfa0e7f78', 32, 'STUNG SEN', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(66, 'b1728eba16134a413390441d35fff9d7', 32, 'PRASAT BALANG', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(67, 'b7f51837908e46f19cc65882b4e4a943', 32, 'PRASAT SAMBOUR', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(68, '0ac71b9ece4afc53042eade96b98ec7e', 32, 'SANDAN', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(69, '0647eeb32740e636903632be4e28bbde', 32, 'SANTUK', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(70, '7358e1b780ef0280953ac2d40b9fe870', 32, 'STOUNG', '2014-12-22 13:31:05', 1, '2014-12-22 13:31:05', NULL, 1),
(71, '783c676ae725bd7649bf8d1d03705cf8', 33, 'ANGKOR CHEY', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(72, '1ebf9e6eb0d1112c487555ff72e5b68b', 33, 'BANTEAY MEAS', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(73, 'a2593859b6ba5fb6f3f912448fa3a3cf', 33, 'CHHUK', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(74, '5999e446b8e25d186f8c044c59f520fd', 33, 'CHUM KIRI', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(75, 'b475dc4daa54dc8b17125d854ce460aa', 33, 'DANG TONG', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(76, 'e19b4fc4e7ababdcb6146f5b74f9ec82', 33, 'KAMPONG TRACH', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(77, '8377c267a6df7638096868d902a11784', 33, 'TUEK CHHOU', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(78, '65c0f4f746c3e5419475ffd8b888db2a', 33, 'KAMPOT', '2014-12-22 13:33:09', 1, '2014-12-22 13:33:09', NULL, 1),
(79, '90db0f94803b453f40d433426f525650', 34, 'KANDAL STUNG', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(80, '2d88a6b3b0ae028e3c84017bd313e02b', 34, 'KIEN SVAY', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(81, 'a7dc3512723b60625032600f54639120', 34, 'KHSACH KANDAL', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(82, '4ce782c79c1294bb1211171fcf31076d', 34, 'KOH THOM', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(83, '6fd543663d5a18b6b8e40de860039dac', 34, 'LEUK DEK', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(84, '0e84b042a4fc8507d60fc1f998054525', 34, 'LAVEAR EM', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(85, 'cdce47f75eaeec7a55eb4888db5db106', 34, 'MUKH KOMPHOOL', '2014-12-22 13:35:22', 1, '2015-01-10 14:24:48', 2, 1),
(86, '063a54f2152f5139cb3d7f7d9ceb5f88', 34, 'ANG SNOUL', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(87, '73ecf456184b2d27bf886c669eb11d75', 34, 'PONHEA LEU', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(88, '95766d0fb087881b7b4b2a83f82a85af', 34, 'S''ANG', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(89, '70b132a0253b01801a38b89c148365ca', 34, 'TA KHMAO', '2014-12-22 13:35:22', 1, '2014-12-22 13:35:22', NULL, 1),
(90, '2ec862462ba700fb126f58989a1fcce7', 35, 'KEP', '2014-12-22 13:36:17', 1, '2014-12-22 13:36:17', NULL, 1),
(91, '42c3f93c35d5a546356d0f5b68135a2a', 35, 'DAMNAK CHANG'' AEUR', '2014-12-22 13:36:17', 1, '2014-12-22 13:36:17', NULL, 1),
(92, '62d9af8707748bb5214a06032b1268df', 50, 'BOTUM SAKOR', '2014-12-22 13:38:22', 1, '2014-12-22 13:38:22', NULL, 1),
(93, '2ddc14e200bb2b529b641bb79ea6ad52', 50, 'KIRI SAKOR', '2014-12-22 13:38:22', 1, '2014-12-22 13:38:22', NULL, 1),
(94, '90d8c5d5b54bfbcb36eb6932ebf1d374', 50, 'KOH KONG', '2014-12-22 13:38:23', 1, '2014-12-22 13:38:23', NULL, 1),
(95, '4a506f2c7b5e012076c61ffa5adcda66', 50, 'KRONG KAMARAKPUMIN', '2014-12-22 13:38:23', 1, '2014-12-22 13:38:23', NULL, 1),
(96, '52d81dd252a6bf3c8f5af373d337acab', 50, 'MONDOL SEIMA', '2014-12-22 13:38:23', 1, '2014-12-22 13:38:23', NULL, 1),
(97, 'f743571de0b2addb4c4235118185dc7f', 50, 'SRAE AMBEL', '2014-12-22 13:38:23', 1, '2014-12-22 13:38:23', NULL, 1),
(98, '6f38ee6ce74789c40979582a03ead126', 50, 'THMAR BANG', '2014-12-22 13:38:23', 1, '2014-12-22 13:38:23', NULL, 1),
(99, 'db1a30e43ebcc46c1b35be203f7b3006', 36, 'CHHLOUNG', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(100, 'd12dff943c698907c51007e55ad64254', 36, 'KRATIE', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(101, 'cc95135c138c49d181db60c034ca2801', 36, 'PREK PRASAB', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(102, '516539ce22d8678935e65c3a79f9de26', 36, 'SAMBOUR', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(103, '45eca94587bd24eb9b72bb2e38c8ac9f', 36, 'SNUOL', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(104, '395f6986baa37dedc0c6844a03116088', 36, 'CHET BOREI', '2014-12-22 13:41:29', 1, '2014-12-22 13:41:29', NULL, 1),
(105, 'b97f56d8a53b83d7f1cb434becf4b24d', 37, 'KEO SEIMA', '2014-12-22 13:43:16', 1, '2014-12-22 13:43:16', NULL, 1),
(106, '679c3c2bfb3759f49bcc75d9ca7d28b1', 37, 'KOH NHEAK', '2014-12-22 13:43:16', 1, '2014-12-22 13:43:16', NULL, 1),
(107, '96a0b758989fdbc426fca4376b65b06a', 37, 'O REANG', '2014-12-22 13:43:16', 1, '2014-12-22 13:43:16', NULL, 1),
(108, 'fdce2f474192153725d14928816df787', 37, 'PICH CHANDA', '2014-12-22 13:43:16', 1, '2014-12-22 13:43:16', NULL, 1),
(109, '60efe349a65cf8511ab828117bec2ffb', 37, 'SENMONOROM', '2014-12-22 13:43:16', 1, '2014-12-22 13:43:16', NULL, 1),
(110, 'f641e1f06cb6c7e60d840a805442e083', 38, 'ANLONG VENG', '2014-12-22 13:44:22', 1, '2014-12-22 13:44:22', NULL, 1),
(111, 'a3ee0c93c6e836ee3b1e702e06fb1539', 38, 'BANTEAY AMPIL', '2014-12-22 13:44:22', 1, '2014-12-22 13:44:22', NULL, 1),
(112, '016b0c3d8658cc62f1039e2ff42bff7e', 38, 'CHONG KAL', '2014-12-22 13:44:22', 1, '2014-12-22 13:44:22', NULL, 1),
(113, '38dee0756001d7deded428d765208570', 38, 'SAMRAONG', '2014-12-22 13:44:22', 1, '2014-12-22 13:44:22', NULL, 1),
(114, '237657942823ae8014983a036ac678c5', 38, 'TRAPEANG PRASAT', '2014-12-22 13:44:22', 1, '2014-12-22 13:44:22', NULL, 1),
(115, 'b15992c96bce4503213b13abba686e3a', 39, 'PAILIN', '2014-12-22 13:45:04', 1, '2014-12-22 13:45:04', NULL, 1),
(116, 'cc19c6da7fccc696b9ca40ef1a9b1802', 39, 'SALA KRAOU', '2014-12-22 13:45:04', 1, '2014-12-22 13:45:04', NULL, 1),
(117, 'd45a119eb035836ecc8f0d94b84e8b6e', 40, 'MITTAPHEAP', '2014-12-22 13:46:00', 1, '2014-12-22 13:46:00', NULL, 1),
(118, '76aa0e3d26eb0db88aa7673c2789d5ed', 40, 'PREY NOB', '2014-12-22 13:46:00', 1, '2014-12-22 13:46:00', NULL, 1),
(119, '1cd22a69a8c8daa8477976054c251735', 40, 'STUNG HAV', '2014-12-22 13:46:00', 1, '2014-12-22 13:46:00', NULL, 1),
(120, '3859e88c5d7f89d4a19dbaa77d38a3ee', 40, 'KAMPONG SEILA', '2014-12-22 13:46:00', 1, '2014-12-22 13:46:00', NULL, 1),
(121, '01eed7e604532ce16de4c2ff6094b6a5', 41, 'CHEY SEN', '2014-12-22 13:46:59', 1, '2014-12-22 13:46:59', NULL, 1),
(122, '852cfdee6187bdc8dbcac7d9e3eb1498', 41, 'CHHEB', '2014-12-22 13:46:59', 1, '2014-12-22 13:46:59', NULL, 1),
(123, '57c11c87df882bef9588bd63b8ad5ef4', 41, 'CHAM KHSANT', '2014-12-22 13:46:59', 1, '2014-12-22 13:46:59', NULL, 1),
(124, 'b6a332c4a7466439036d461c02f0c2a4', 41, 'KULEN', '2014-12-22 13:48:17', 1, '2014-12-22 13:48:17', NULL, 1),
(125, '55da8233ed4273a5a4854a65578cf06c', 41, 'ROVIENG', '2014-12-22 13:48:17', 1, '2014-12-22 13:48:17', NULL, 1),
(126, 'c563cedb34de2bbe5e4ea6e11065cc5f', 41, 'SANGKOM THMEY', '2014-12-22 13:48:17', 1, '2014-12-22 13:48:17', NULL, 1),
(127, '7ec9a9156aa54dbb94e391a0488b1720', 41, 'TBENG MEANCHEY', '2014-12-22 13:48:17', 1, '2014-12-22 13:48:17', NULL, 1),
(128, '7ee8e0f6bd2070e385f2284d8edfa003', 42, 'BAKAN', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(129, '81571e3877c1d31196dcb248236dc440', 42, 'KANDIENG', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(130, '4d354546ba7f28af86d345fb2ca14459', 42, 'KRAKOR', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(131, 'f40e42fadade6065550ee944547b56af', 42, 'PHNOM PRAVANH', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(132, '88e7cd7819cbb9588c09f4830e393412', 42, 'SAMPOV MEAS', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(133, '9072a97b7e366211ae4c72d42163c459', 42, 'VEAL VENG', '2014-12-22 13:49:20', 1, '2014-12-22 13:49:20', NULL, 1),
(134, 'df4ac84620db75f0cb7661d7eb46fde4', 43, 'BA PHNOM', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(135, '0f9c915a7111383c5d87acc9559ad9d5', 43, 'KAMCHAY MEAR', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(136, '5d362af836eedb3094d6054e3fb5b4cf', 43, 'KAMPONG TRABEK', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(137, 'c42aebdbbf984a6d5a860171fa24aead', 43, 'KANHCHRIECH', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(138, 'fbe5d78c8a779d3d436b3c3fa7843b00', 43, 'ME SANG', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(139, '0e8506af3adc2ad6ac709db92e9dc26f', 43, 'PEAM CHOR', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(140, '1b6c13afa808f72a6b328fb35bbefb51', 43, 'PEAM ROR', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(141, '385e95f0912c0633ac461b0caa4fedb2', 43, 'PEA RANG', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(142, 'a46c6b82f4779b6f2327893de600784f', 43, 'PREAH SDACH', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(143, '6663a15654c30596b13db2bd1628133b', 43, 'PREY VENG', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(144, '89ceb59e2b089226c36a516b8963d057', 43, 'KAMPONG LEAV', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(145, '4cd175ee8c8b35835fbf1d0868749e11', 43, 'SITHOR KANDAL', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(146, 'e4acf3c3b29e9718a2bc36b82e32845f', 43, 'SVAY ANTOR', '2014-12-22 13:52:00', 1, '2014-12-22 13:52:00', NULL, 1),
(147, 'af83dd3a8d049a53702e386c3cf39264', 44, 'ANDOUNG MEAS', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(148, '3bb2bb20c603808b883e52a8ff0f0200', 44, 'BANLUNG', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(149, 'fbf76ee19925f5990c21124c139d48ce', 44, 'BAR KAEV', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(150, '554efaf5c66c3050642e9a81795b30c9', 44, 'KOUN MOM', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(151, 'c0967d6c6b0102ffc73447763dd74eb6', 44, 'LUMPHAT', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(152, '71144a9787c61a20d201c506c2966205', 44, 'O CHUM', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(153, '7cf44e174c7530ba4bac0c6f2810163a', 44, 'O YA DA', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(154, '4729fdb0b3175a8c534dd96101fff51e', 44, 'TA VEANG', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(155, '30f33cd22982819a0960b918cd968b57', 44, 'VEUN SAI', '2014-12-22 13:53:52', 1, '2014-12-22 13:53:52', NULL, 1),
(156, 'a11a99be84b13766d263a56200a7e7fc', 45, 'ANGKOR CHUM', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(157, '05fbcde5cc923a4eeccec964b9445121', 45, 'ANGKOR THOM', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(158, 'b94f444c37b216281dad8e8b809f630d', 45, 'BANTEAY SREI', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(159, '0d97ba6d7b26d943b6d061a8918d47a1', 45, 'CHI KRENG', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(160, '1a7baf6acd8a72d5722fb180fe9cde72', 45, 'KRALANH', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(161, '949a17e4721b85ef623ac46e51805786', 45, 'PUOK', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(162, '89ed0baee5c9eeb0fe11bdb7a9cf74ce', 45, 'PRASAT BAKONG', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(163, 'a8f6a55695b4543aca37464dd29b3211', 45, 'SIEM REAP', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(164, 'a5b0f719346b20509cf218ac7c00e588', 45, 'SOUT NIKOM', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(165, '0ff0451b26371323dee3d3ea041583ae', 45, 'SREI SNAM', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(166, '59b8f3269e7d850112a83512164f1615', 45, 'SVAY LEU', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(167, '2c6fcb38a03570ab231cf874e95c18dc', 45, 'VARIN', '2014-12-22 13:56:02', 1, '2014-12-22 13:56:02', NULL, 1),
(168, 'edd8a3d8745d90f91ff0df6cfb545fc3', 46, 'SESAN', '2014-12-22 13:57:16', 1, '2014-12-22 13:57:16', NULL, 1),
(169, '11001bbcb586ab9bde1658dd88277388', 46, 'SIEM BOUK', '2014-12-22 13:57:16', 1, '2014-12-22 13:57:16', NULL, 1),
(170, 'cfe773da4fc01b4cbec7006a5680904b', 46, 'SIEM PANG', '2014-12-22 13:57:16', 1, '2014-12-22 13:57:16', NULL, 1),
(171, '41acb8b0330836f809412e5162cdcfdf', 46, 'STUNG TRENG', '2014-12-22 13:57:16', 1, '2014-12-22 13:57:16', NULL, 1),
(172, '5398ba9423e0e98df6989aae8640deff', 46, 'THALA BARIVAT', '2014-12-22 13:57:16', 1, '2014-12-22 13:57:16', NULL, 1),
(173, 'a5a5f8af6d5ae7f684f82a7164c81e0c', 47, 'CHANTREA', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(174, '44c0c6ba7dfb0fff84a0edf464c538f0', 47, 'KAMPONG ROU', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(175, '8deb3e353ae7a109edd30dc2877664f8', 47, 'ROMDOUL', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(176, '9d2148cc22c01d513e9711972aa12ea0', 47, 'ROMEAS HAEK', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(177, 'ad3df56f98465fdd3ecaf8e73e2b40b7', 47, 'SVAY CHROM', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(178, 'f6e864527a83a93d7a3bf14e1efec985', 47, 'SVAY RIENG', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(179, '55954cc690e8c18a375a1bda7c963d9c', 47, 'SVAY TEAP', '2014-12-22 13:58:55', 1, '2014-12-22 13:58:55', NULL, 1),
(180, '372bd084d6c46c118ca2968c4326726b', 48, 'ANGKOR BOREI', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(181, '8714127b3d06e6b8b16c5a6b194a5a2e', 48, 'BATI', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(182, 'daef8674f5e5b07d4c92e9e4747c7f60', 48, 'BOREI CHOLSAR', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(183, '15205b05b2b86c7d68299bf366e77c81', 48, 'KIRIVONG', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(184, 'f329888501b0f2cf463ce83374f34bd2', 48, 'KOH ANDET', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(185, 'c5c61437969dd32c531378ed767cd0db', 48, 'PREY KABBAS', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(186, '932b85ef072632e5947cd80897f62daa', 48, 'SAMRAONG', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(187, '58bb66e0a9104aff97c93619ad68300e', 48, 'DAUN KEO', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(188, 'b166ca4b71a325c6a5ff74c21aacfa12', 48, 'TRAM KAK', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(189, '81e023fbde91c164b664b990ee7859b0', 48, 'TREANG', '2014-12-22 14:02:24', 1, '2014-12-22 14:02:24', NULL, 1),
(190, '9c1cd044ffe1503f162b5f61fc37c964', 49, 'DAMBAE', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(191, '12150a778c503e4f5863246dc1cef438', 49, 'KROCH CHHMAR', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(192, 'ab2fe97b8a8162a7cf80983dc6cdc3fb', 49, 'MEMOT', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(193, '3cacc123bcda97292373f3967ac8e132', 49, 'O REANG OV', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(194, 'de98a17be950d693ddd37a011d578ad1', 49, 'PONHEA KRAEK', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(195, '057e5313943d2b55ee318e586743e3f3', 49, 'TBONG KHMUM', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(196, '2ce99815c4fee6abc8757fe08bad9aad', 49, 'SUONG', '2014-12-22 14:04:18', 1, '2014-12-22 14:04:18', NULL, 1),
(197, '2f7fa7114f9e7ab9517fed7be436653d', 43, 'SVAY POL', '2015-01-15 12:30:24', 2, '2015-01-15 12:30:24', NULL, 1),
(198, '67d92dff16581bbd34599994c1166a85', 47, 'KRAL KOR', '2015-01-15 12:36:45', 2, '2015-01-15 12:36:45', NULL, 1),
(199, 'e132113158d110ad0444ad1ec4ecac72', 47, 'BAVET', '2015-01-15 12:43:56', 2, '2015-01-15 12:43:56', NULL, 1),
(200, 'd0b9de0cec2f0fd6d8a3b587a8ca41ef', 31, 'KAMPONG SPEU', '2015-01-24 15:34:50', 2, '2015-01-24 15:34:50', NULL, 1),
(201, 'cf298644ad3db8db65acd3d0a476147a', 40, 'PREAH SIHANOUK', '2015-01-30 17:05:26', 2, '2015-01-30 17:05:26', NULL, 1),
(202, '21dfac69cff6ade152b9b58ea0ab958f', 26, 'KHAN 7 MAKARA', '2015-03-23 14:54:45', 2, '2015-03-23 14:54:45', NULL, 1);

--
-- Triggers `districts`
--
DROP TRIGGER IF EXISTS `zDistrictBfInsert`;
DELIMITER //
CREATE TRIGGER `zDistrictBfInsert` BEFORE INSERT ON `districts`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.province_id = "" OR NEW.province_id = NULL THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_chief_complains`
--

CREATE TABLE IF NOT EXISTS `doctor_chief_complains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queued_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `chief_complain_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `patient_consultation_id` int(11) DEFAULT NULL,
  `chief_complain` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_comments`
--

CREATE TABLE IF NOT EXISTS `doctor_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `patient_consultation_id` int(11) DEFAULT NULL,
  `doctor_comment` text COLLATE utf8_unicode_ci,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `type` int(11) DEFAULT '0' COMMENT '0: from ipd; 1: doc comment',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `doctor_comments`
--

INSERT INTO `doctor_comments` (`id`, `queue_id`, `queued_doctor_id`, `patient_consultation_id`, `doctor_comment`, `name`, `description`, `type`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, NULL, NULL, NULL, NULL, 'Good', 'Just leb tnam oy ban teang tal', 0, '2023-06-03 15:34:02', 1, '2023-06-03 15:34:02', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_consultations`
--

CREATE TABLE IF NOT EXISTS `doctor_consultations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_daignostics`
--

CREATE TABLE IF NOT EXISTS `doctor_daignostics` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` bigint(20) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `patient_consultation_id` bigint(20) DEFAULT NULL,
  `doctor_daignostic` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_medical_histories`
--

CREATE TABLE IF NOT EXISTS `doctor_medical_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queued_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `medical_history_id` int(11) DEFAULT NULL,
  `patient_consultation_id` int(11) DEFAULT NULL,
  `medical_history` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echographie_patients`
--

CREATE TABLE IF NOT EXISTS `echographie_patients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `echography_infom_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `indication_id` int(11) DEFAULT NULL,
  `ddr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `form_child` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_child` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `healthy_child` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex_child` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teok_plos` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_sok` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight_child` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_child` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_child` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `born_date` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echography_infoms`
--

CREATE TABLE IF NOT EXISTS `echography_infoms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:is_active 2:edit 3:delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_services`
--

CREATE TABLE IF NOT EXISTS `echo_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `echo_service_queue_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conclusion` text COLLATE utf8_unicode_ci,
  `echo_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_cardias`
--

CREATE TABLE IF NOT EXISTS `echo_service_cardias` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motif_exam` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `effecture` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `conclusion` text COLLATE utf8_unicode_ci,
  `vd_dtd` double DEFAULT '0',
  `ao_ascend` double DEFAULT '0',
  `og_1` double DEFAULT '0',
  `og_2` double DEFAULT '0',
  `siv_1` double DEFAULT '0',
  `siv_2` double DEFAULT '0',
  `vgdtd_dts_1` double DEFAULT '0',
  `vgdtd_dts_2` double DEFAULT '0',
  `pp_vg_1` double DEFAULT '0',
  `pp_vg_2` double DEFAULT '0',
  `frvg_fevg_1` double DEFAULT '0',
  `frvg_fevg_2` double DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_cardia_images`
--

CREATE TABLE IF NOT EXISTS `echo_service_cardia_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `echo_srv_cardia_id` int(11) DEFAULT NULL,
  `src_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_consultations`
--

CREATE TABLE IF NOT EXISTS `echo_service_consultations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `echo_service_queue_id` int(11) DEFAULT NULL,
  `service_de_provenant` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `consultation` text COLLATE utf8_unicode_ci,
  `echo_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_consultation_images`
--

CREATE TABLE IF NOT EXISTS `echo_service_consultation_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `echo_srv_con_id` int(11) DEFAULT NULL,
  `dir_file` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_details`
--

CREATE TABLE IF NOT EXISTS `echo_service_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `service_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` datetime DEFAULT NULL,
  `modify_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_images`
--

CREATE TABLE IF NOT EXISTS `echo_service_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `echo_srv_id` int(11) DEFAULT NULL,
  `src_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:activ 2:delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_queues`
--

CREATE TABLE IF NOT EXISTS `echo_service_queues` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_requests`
--

CREATE TABLE IF NOT EXISTS `echo_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `echo_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `echo_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `echo_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `echo_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `egroups`
--

CREATE TABLE IF NOT EXISTS `egroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `egroups`
--

INSERT INTO `egroups` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '26cdb3d6db158c4fff0c64d84642382e', 'Office', '2018-12-24 15:10:01', 5, '2018-12-24 15:26:46', 1, 1),
(2, 'f408c4f2d6884a05f77fa6348301373c', 'Doctor', '2018-12-24 15:29:56', 1, '2018-12-24 15:29:56', NULL, 1),
(3, '9b3e9624115e0b61fede064c2d2fe11c', 'Labo team', '2019-03-11 11:54:04', 11, '2019-03-11 11:54:04', NULL, 1),
(4, '6f72b64772d848fa6b1d6cc20c8eb55a', 'Delopment', '2019-05-03 16:13:40', 1, '2019-05-03 16:13:47', 1, 1);

--
-- Triggers `egroups`
--
DROP TRIGGER IF EXISTS `zEgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zEgroupBfInsert` BEFORE INSERT ON `egroups`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `egroup_companies`
--

CREATE TABLE IF NOT EXISTS `egroup_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `egroup_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `egroup_id` (`egroup_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `egroup_companies`
--

INSERT INTO `egroup_companies` (`id`, `egroup_id`, `company_id`) VALUES
(2, 1, 1),
(3, 2, 1),
(4, 3, 1),
(6, 4, 1);

--
-- Triggers `egroup_companies`
--
DROP TRIGGER IF EXISTS `zEgroupCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zEgroupCompanyBfInsert` BEFORE INSERT ON `egroup_companies`
 FOR EACH ROW BEGIN
	IF NEW.egroup_id = "" OR NEW.egroup_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `house_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `photo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_kh` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `start_working_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `personal_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `salary` decimal(15,3) DEFAULT NULL,
  `work_for_vendor_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_show_in_sales` tinyint(4) DEFAULT '0' COMMENT '1: Sale Rep; 2: Delivery; 3: Collector;',
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `employee_code` (`employee_code`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `sys_code`, `house_no`, `street_id`, `province_id`, `district_id`, `commune_id`, `village_id`, `photo`, `employee_code`, `name`, `name_kh`, `sex`, `dob`, `start_working_date`, `termination_date`, `personal_number`, `other_number`, `email`, `position_id`, `salary`, `work_for_vendor_id`, `note`, `created`, `created_by`, `modified`, `modified_by`, `is_show_in_sales`, `is_active`) VALUES
(1, 'a1faa6596cf7196ce7f72844fe317440', '', NULL, NULL, NULL, NULL, NULL, '', 'EMP0000001', 'Dr. Mony', 'ចាន់​ មូនី', 'Male', '1999-04-07', '0000-00-00', '0000-00-00', '010101010', '', '', 1, '1500.000', 1, '', '2023-06-07 11:58:38', 1, '2023-06-07 11:58:38', NULL, 0, 1),
(2, '6b88e13d984b0abbb2039c5002f1ab8b', '', NULL, NULL, NULL, NULL, NULL, '', 'EMP0000002', 'Dr. Reaksmey', 'លី​ រស្មី', 'Female', '1992-05-14', '0000-00-00', '0000-00-00', '015151515', '', '', 1, '1500.000', 1, '', '2023-06-07 11:59:39', 1, '2023-06-07 12:16:52', 1, 0, 1),
(3, 'fcef4df73cf534c6a2feacab31c614c3', '', NULL, NULL, NULL, NULL, NULL, '', 'EMP0000003', 'Danin', 'ឈុន ដានីន', 'Female', '2000-04-17', '0000-00-00', '0000-00-00', '010101010', '', '', 2, '500.000', 1, '', '2023-06-07 12:01:32', 1, '2023-06-07 12:07:05', 1, 0, 1),
(4, '35fb46bc8c4ced910e9cbb115057a720', '', NULL, NULL, NULL, NULL, NULL, '', 'EMP0000004', 'Malina', 'វង់ ម៉ាលីណា', 'Female', '2005-04-23', '0000-00-00', '0000-00-00', '017171717', '', '', 2, NULL, 1, '', '2023-06-07 12:02:53', 1, '2023-06-07 12:02:53', NULL, 0, 1);

--
-- Triggers `employees`
--
DROP TRIGGER IF EXISTS `zEmployeeBfInsert`;
DELIMITER //
CREATE TRIGGER `zEmployeeBfInsert` BEFORE INSERT ON `employees`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.employee_code = "" OR NEW.employee_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.name_kh = "" OR NEW.name_kh = NULL OR NEW.sex = "" OR NEW.sex = NULL OR NEW.dob = "" OR NEW.dob = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employee_companies`
--

CREATE TABLE IF NOT EXISTS `employee_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `employee_companies`
--

INSERT INTO `employee_companies` (`id`, `employee_id`, `company_id`) VALUES
(1, 1, 1),
(4, 4, 1),
(7, 3, 1),
(8, 2, 1);

--
-- Triggers `employee_companies`
--
DROP TRIGGER IF EXISTS `zEmployeeCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zEmployeeCompanyBfInsert` BEFORE INSERT ON `employee_companies`
 FOR EACH ROW BEGIN
	IF NEW.employee_id = "" OR NEW.employee_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employee_egroups`
--

CREATE TABLE IF NOT EXISTS `employee_egroups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) DEFAULT NULL,
  `egroup_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `egroup_id` (`egroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `employee_egroups`
--

INSERT INTO `employee_egroups` (`id`, `employee_id`, `egroup_id`) VALUES
(1, 1, 2),
(5, 4, 1),
(6, 4, 3),
(11, 3, 1),
(12, 3, 3),
(13, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

CREATE TABLE IF NOT EXISTS `examinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `examinations`
--

INSERT INTO `examinations` (`id`, `type`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, NULL, 'CCCCCCCCCCC', 'BBBBBBBBBBBBB', '2019-02-18 10:05:12', 1, '2019-02-18 13:01:10', 1, 0),
(2, NULL, 'Normal Kid', 'Good general condition. No fever.  Good appetit. Good respiratory .Good hemodynamic. Soft abdomen. No any neurological signs noted. .', '2019-02-18 13:06:15', 1, '2019-02-18 17:15:24', 1, 1),
(3, NULL, 'Acute Watery diarrhea : ', 'The child looks sick. Watery diarrhea around 8 time a day. Poor appetit. Dehydration around 7% of body weight. Poor urine output. No respiratory distress. Good hemodynamic status.', '2019-02-18 13:10:58', 1, '2019-02-18 17:17:19', 1, 1),
(4, NULL, 'Asthma ', 'The child looks tired. Difficult to breath. Poor feeding. Sibilant rales heard from both lungs. Good hemodynamic status', '2019-02-18 13:13:18', 1, '2019-02-18 17:12:35', 1, 1),
(5, NULL, 'Food poisoning', 'The child looks sick. Nausea and vomiting many time. Some diarrhea. Mild dehydration. No respiratory distress. Good hemodynamic status.', '2019-02-18 13:16:14', 1, '2019-02-18 17:14:21', 1, 1),
(6, NULL, 'Acute Pharyngitis  ', ' The child looks sick. High fever. Cough repeatedly. Runny noses. Vomiting while coughing. Poor feeding. Pharynx inflammed. Both tonsils mild enlarges. Lungs are clear. Good hemodynamic status.', '2019-02-18 13:21:47', 1, '2019-02-18 17:10:38', 1, 1),
(7, NULL, 'Common cold ', 'Good general condition. Mild fever. Cough and runny noses. Nasal congestion. Pharynx no inflammed. Lung clear. Good hemodynamic status.', '2019-02-18 13:23:36', 1, '2019-04-27 10:56:55', 1, 1),
(8, NULL, 'HFMD ', 'The child looks sick. High fever. Poor appetit. Hypersalivation. Mild dehydration. Some blisters in mouth cavity and in pharynx. No respiratory distress. Good hemodynamic status. No any neurological signs noted.', '2019-02-18 13:26:15', 1, '2019-02-18 17:15:05', 1, 1),
(9, NULL, 'Allergic Rhinitis ', 'The child looks well. Coughing alot especially at night. Nasal congestion. Runny noses a lot. Both nostriles congested with much secretion. Pharynx normal. lungs clear. No abnormalities heart sound.', '2019-02-18 13:31:01', 1, '2019-02-18 17:12:14', 1, 1),
(10, NULL, 'Dysenteria ', ' The child looks sick. Mild fever. Stool with mucus and blood. poor feeding. Mild dehydration. No any others abnormality found in physical examination', '2019-02-18 13:37:12', 1, '2019-02-18 17:14:03', 1, 1),
(11, NULL, 'Skin allergic ', 'Good general condition. No fever. Good appetit. Skin rash in the whole body.  No any others abnormalities found in physical examination.', '2019-02-18 13:40:25', 1, '2019-02-18 17:15:48', 1, 1),
(12, NULL, 'Acute Bronchitis', ' Good general condition. Mild fever. Coughing a lot. Mild difficult to breath. Some bronchial rales at the right lung. No any others abnormality found in physical examination.', '2019-02-18 14:26:39', 1, '2019-02-18 17:09:43', 1, 1),
(13, NULL, 'Dengue fever', ' The child looks sick. High fever. Poor appetit. Soft abdomen. Mild hepatomegalia. Good hemodynamic status. Good respiratory condition. No any external bleeding seen. No any others abnormalities found in physical examination.', '2019-02-18 14:42:52', 1, '2019-02-18 17:13:39', 1, 1),
(14, NULL, 'Otitis', 'Good general condition. Fever. Ear pain with purulent ear discharge. Pharynx normal. Lung and heart are clear.', '2019-02-18 17:08:16', 1, '2019-02-18 17:08:16', NULL, 1),
(15, NULL, 'Acute laryngitis', 'Good general condition. High fever. Dysphonia. Stridor. Inspiratory dyspnea. no any others abnormalities found in physical examination.', '2019-02-18 17:19:17', 1, '2019-02-18 17:19:17', NULL, 1),
(16, NULL, 'Acute bronchiolitis', 'The child looks tired. Difficult to breath. Mild fever.  Poor feeding. Sibilant rales heard from both lungs. No any others abnormality found in physical examination.', '2019-02-18 17:24:29', 1, '2019-02-18 17:24:29', NULL, 1),
(17, NULL, 'B', 'B', '2019-05-03 16:21:14', 1, '2019-05-03 16:21:14', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `rate_to_sell` decimal(15,9) DEFAULT '0.000000000',
  `rate_to_change` decimal(15,9) DEFAULT '0.000000000',
  `rate_purchase` decimal(15,9) DEFAULT '0.000000000',
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_searchs` (`currency_center_id`),
  KEY `company_id` (`branch_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `sys_code`, `branch_id`, `currency_center_id`, `rate_to_sell`, `rate_to_change`, `rate_purchase`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '9d823e74aa8ec0e872b83ac3e24cf337', 1, 2, '4100.000000000', '4050.000000000', '4100.000000000', '2018-11-16 10:41:37', 1, '2018-11-16 10:41:37', NULL, 1),
(2, '8ab64703f6848a1d9d520761c72f40cc', 1, 2, '4100.000000000', '4050.000000000', '4100.000000000', '2018-11-16 10:42:02', 1, '2018-11-16 10:42:02', NULL, 1),
(3, '262e4127bd199ab0531a9254963c84c4', 1, 2, '4100.000000000', '4050.000000000', '4100.000000000', '2018-11-16 10:42:06', 1, '2018-11-16 10:42:06', NULL, 1),
(4, '340318f6999ab81c12596c1dd0843788', 1, 2, '4100.000000000', '4050.000000000', '4100.000000000', '2019-02-11 11:02:55', 1, '2019-02-11 11:02:55', NULL, 1),
(5, 'ebb584c0009bbc5f0379be6ef3ac4779', 1, 2, '4100.000000000', '4050.000000000', '4100.000000000', '2019-02-11 11:02:57', 1, '2019-02-11 11:02:57', NULL, 1),
(6, '68305fa54deedfd629b570c6bc94005f', 1, 2, '4000.000000000', '4000.000000000', '4000.000000000', '2019-02-11 11:03:38', 1, '2019-02-11 11:03:38', NULL, 1),
(7, '9bdcf238f74d8a69799b1662228ebf97', 1, 2, '4000.000000000', '4000.000000000', '4000.000000000', '2019-02-18 18:17:05', 1, '2019-02-18 18:17:05', NULL, 1),
(8, '20a0ca7371c74f7453030a30c81bc1ea', 1, 2, '4000.000000000', '4000.000000000', '4000.000000000', '2019-05-03 16:16:30', 1, '2019-05-03 16:16:30', NULL, 1);

--
-- Triggers `exchange_rates`
--
DROP TRIGGER IF EXISTS `zExchangeRateBfInsert`;
DELIMITER //
CREATE TRIGGER `zExchangeRateBfInsert` BEFORE INSERT ON `exchange_rates`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.branch_id = "" OR NEW.branch_id = NULL OR NEW.currency_center_id = "" OR NEW.currency_center_id = NULL OR NEW.rate_to_sell = "" OR NEW.rate_to_sell = NULL OR NEW.rate_to_change = "" OR NEW.rate_to_change = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reference` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1: Edit; 0: Void; 1: Issued',
  PRIMARY KEY (`id`),
  KEY `filters` (`date`,`reference`),
  KEY `searchs` (`vendor_id`,`customer_id`,`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `expense_details`
--

CREATE TABLE IF NOT EXISTS `expense_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `amount` decimal(15,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `expense_id` (`expense_id`),
  KEY `chart_account_id` (`chart_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `e_pgroup_shares`
--

CREATE TABLE IF NOT EXISTS `e_pgroup_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `e_product_category_id` int(11) DEFAULT NULL,
  `pgroup_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pgroup_id` (`pgroup_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `e_product_categories`
--

CREATE TABLE IF NOT EXISTS `e_product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `e_product_categories`
--

INSERT INTO `e_product_categories` (`id`, `sys_code`, `name`, `created`, `modified`, `is_active`) VALUES
(1, 'dcbc43ff360f5534d0e719d2be622e3b', 'Tech Accessories', '2017-02-14 13:15:13', '2017-02-14 13:15:16', 1),
(2, 'c37b761d3d1654ae6d9fe2e6e2dc6de8', 'Women''s Fashion', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(3, 'd46a1fa706ef89df7adc03495ccf1510', 'Jewelry & Watches', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(4, '00c91b0c7ce6ab1b310af08ac6936c3b', 'Cosmetics', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(5, '422957798cc704357d02fcc2430662a3', 'Home Decor', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(6, '792a7c6281baa8efcbe7dfdcd5365864', 'Men''s Fashion', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(7, '0651a3ab91390af1b0002190effb83d2', 'Auto Parts', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(8, '0d8739bc991aa3ce72f637379f2e92d2', 'Kids & Baby', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(9, 'c722128692943cc55f19aba642b4bb95', 'Mobile Phones', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(10, '87fd25f53ac2b03c18b59e2d123920cb', 'Shoes & Bags', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(11, '3791d695dbc0de4be10bb4918d70c4e3', 'Sports', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(12, '01f0d76bdeee8e05b40f89bad7a1c985', 'Consumer Electronics', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(13, '051860191cce8adfcdff0cbb86a9d958', 'Human Hair', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1),
(14, '6df1c0f27f0af05d171b5fe30f51da59', 'Tablets', '2017-02-14 13:15:14', '2017-02-14 13:15:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `e_product_detail_shares`
--

CREATE TABLE IF NOT EXISTS `e_product_detail_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `view` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `rate` int(11) DEFAULT NULL,
  `rate_level` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `e_product_prices`
--

CREATE TABLE IF NOT EXISTS `e_product_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `before_price` decimal(15,3) DEFAULT NULL,
  `sell_price` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uom_id` (`uom_id`),
  KEY `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `e_product_prices`
--
DROP TRIGGER IF EXISTS `zEProductPriceBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zEProductPriceBeforeDelete` BEFORE DELETE ON `e_product_prices`
 FOR EACH ROW BEGIN
	INSERT INTO e_product_price_histories VALUES (NULL, OLD.product_id, OLD.uom_id, OLD.before_price, OLD.sell_price, OLD.created, OLD.created_by);
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `e_product_price_histories`
--

CREATE TABLE IF NOT EXISTS `e_product_price_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `before_price` decimal(15,3) DEFAULT NULL,
  `sell_price` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uom_id` (`uom_id`),
  KEY `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `e_product_shares`
--

CREATE TABLE IF NOT EXISTS `e_product_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `total_view` int(11) DEFAULT NULL,
  `total_order` int(11) DEFAULT NULL,
  `total_rate` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1: Active; 2: Disactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`product_id`),
  KEY `product_id` (`product_id`),
  KEY `company_id` (`company_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `e_store_shares`
--

CREATE TABLE IF NOT EXISTS `e_store_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `e_mail` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `long` int(11) DEFAULT NULL,
  `lat` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_share` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE IF NOT EXISTS `fixed_assets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `fixed_asset_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `warranty_expires` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asset_account` int(11) DEFAULT NULL,
  `accum_account` int(11) DEFAULT NULL,
  `depr_account` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `cost` decimal(15,3) DEFAULT NULL,
  `cost_remain` decimal(15,3) DEFAULT '0.000',
  `asset_life` int(11) DEFAULT NULL,
  `depr_method` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salvage_value` decimal(15,3) DEFAULT NULL,
  `business_use_percentage` decimal(15,3) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(11) DEFAULT NULL,
  `is_in_used` tinyint(4) DEFAULT '0',
  `is_depre` tinyint(4) DEFAULT '1',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`location_id`,`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_asset_amounts`
--

CREATE TABLE IF NOT EXISTS `fixed_asset_amounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fixed_asset_id` int(11) DEFAULT NULL,
  `general_ledger_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1: SLM; 2:DBM; 3:DDBM',
  `date_post` date DEFAULT NULL,
  `amount_post` decimal(15,12) DEFAULT '0.000000000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `follow_permissions`
--

CREATE TABLE IF NOT EXISTS `follow_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `follow_permissions`
--

INSERT INTO `follow_permissions` (`id`, `type`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 'doctor', '2018-02-20 09:00:00', 1, '2019-04-28 20:53:26', 1, 2),
(3, 'nurse', '2018-02-20 11:40:00', 1, '2019-02-07 08:26:59', 1, 2),
(4, 'labo', '2018-02-20 11:47:00', 1, '2019-02-07 08:27:15', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `general_ledgers`
--

CREATE TABLE IF NOT EXISTS `general_ledgers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_memo_with_sale_id` int(11) DEFAULT NULL,
  `invoice_pbc_with_pbs_id` int(11) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `sales_order_receipt_id` bigint(20) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `credit_memo_id` bigint(20) DEFAULT NULL,
  `credit_memo_receipt_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `pv_id` bigint(20) DEFAULT NULL,
  `purchase_return_id` bigint(20) DEFAULT NULL,
  `purchase_return_receipt_id` bigint(20) DEFAULT NULL,
  `ar_ap_gl_id` bigint(20) DEFAULT NULL,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `receive_payment_id` bigint(20) DEFAULT NULL,
  `pay_bill_id` bigint(20) DEFAULT NULL,
  `ar_aging_id` bigint(20) DEFAULT NULL,
  `ap_aging_id` bigint(20) DEFAULT NULL,
  `apply_to_id` bigint(20) DEFAULT NULL COMMENT 'Purchase Order; Purchase Bill; Quote; Sales Invoice',
  `landing_cost_id` bigint(20) DEFAULT NULL,
  `landing_cost_receipt_id` bigint(20) DEFAULT NULL,
  `expense_id` bigint(20) DEFAULT NULL,
  `inventory_physical_id` bigint(20) DEFAULT NULL,
  `apply_reference` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Purchase Order; Purchase Bill; Quote; Sales Invoice Code',
  `receive_from_id` bigint(20) DEFAULT NULL COMMENT 'Vendor or Customer Id',
  `receive_from_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Vendor or Customer Name',
  `date` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_deposit` decimal(15,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_sys` tinyint(4) DEFAULT '0',
  `is_adj` tinyint(4) DEFAULT '0',
  `is_approve` tinyint(4) DEFAULT '1',
  `is_depreciated` tinyint(4) DEFAULT '0',
  `is_retained_earnings` tinyint(4) DEFAULT '0',
  `deposit_type` tinyint(4) DEFAULT '0' COMMENT '0:Journal; 1: Normal; 2: Purchase Order; 3: Purchase Bill; 4: Quote; 5: Invoice',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_filter` (`sales_order_id`,`sales_order_receipt_id`,`credit_memo_id`,`credit_memo_receipt_id`,`purchase_order_id`,`pv_id`),
  KEY `key_filter_second` (`purchase_return_id`,`purchase_return_receipt_id`,`ar_ap_gl_id`,`cycle_product_id`,`receive_payment_id`,`pay_bill_id`,`ar_aging_id`,`ap_aging_id`),
  KEY `key_filter_third` (`apply_to_id`,`receive_from_id`,`date`,`reference`,`is_approve`,`deposit_type`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_details`
--

CREATE TABLE IF NOT EXISTS `general_ledger_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `general_ledger_id` bigint(20) DEFAULT NULL,
  `main_gl_id` bigint(20) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `is_free` tinyint(4) NOT NULL DEFAULT '0',
  `inventory_valuation_id` bigint(20) DEFAULT NULL,
  `inventory_valuation_is_debit` tinyint(4) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'General Journal',
  `debit` decimal(20,9) DEFAULT '0.000000000',
  `credit` decimal(20,9) DEFAULT '0.000000000',
  `memo` text COLLATE utf8_unicode_ci,
  `customer_id` bigint(20) DEFAULT NULL,
  `queue_id` int(11) DEFAULT '0',
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `other_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) DEFAULT NULL,
  `is_reconcile` tinyint(4) DEFAULT '0',
  `reconcile_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_filter_second` (`location_group_id`,`location_id`,`product_id`,`service_id`,`inventory_valuation_id`),
  KEY `key_filter_third` (`customer_id`,`vendor_id`,`employee_id`,`other_id`,`class_id`),
  KEY `key_filter` (`general_ledger_id`,`main_gl_id`,`chart_account_id`,`company_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_bs1`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_bs1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `other_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_bs11`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_bs11` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `other_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_cus1`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_cus1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_cus2`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_cus2` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_pl1`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_pl1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `other_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_pl8`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_pl8` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `other_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_ven1`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_ven1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger_detail_ven5`
--

CREATE TABLE IF NOT EXISTS `general_ledger_detail_ven5` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_account_id` (`chart_account_id`),
  KEY `company_id` (`company_id`),
  KEY `location_id` (`location_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `genito_urinary_systems`
--

CREATE TABLE IF NOT EXISTS `genito_urinary_systems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queued_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL COMMENT 'Enlarge / Normal',
  `surface` varchar(50) DEFAULT NULL COMMENT 'Smooth/Irregular',
  `consistency` varchar(50) DEFAULT NULL COMMENT 'Firm/ Elastic',
  `median_sulcus` varchar(50) DEFAULT NULL COMMENT 'Obliterated/ Absent',
  `pain` varchar(50) DEFAULT NULL COMMENT 'Yes/No',
  `no_dule` varchar(50) DEFAULT NULL COMMENT 'Yes/No',
  `other` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1 = active ; 2 = delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='type in tab doctor consult module ' AUTO_INCREMENT=156 ;

--
-- Dumping data for table `genito_urinary_systems`
--

INSERT INTO `genito_urinary_systems` (`id`, `queued_id`, `queued_doctor_id`, `size`, `surface`, `consistency`, `median_sulcus`, `pain`, `no_dule`, `other`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 7, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Enlarge Prostate \r\nNodule L and R ', '2018-09-22 10:40:19', 2, '2018-09-22 10:40:19', NULL, 1),
(2, 11, 11, 'normal', 'smooth', NULL, NULL, 'no', NULL, NULL, '2018-09-22 14:25:17', 2, '2018-09-22 14:25:17', NULL, 1),
(3, 12, 12, 'normal', 'smooth', NULL, NULL, 'no', NULL, NULL, '2018-09-22 14:39:53', 2, '2018-09-22 14:39:53', NULL, 1),
(4, 17, 17, 'normal', 'smooth', 'elastic', NULL, 'no', NULL, NULL, '2018-09-23 12:05:06', 2, '2018-09-23 12:05:06', NULL, 1),
(5, 26, 26, NULL, NULL, NULL, NULL, NULL, NULL, 'Normal ', '2018-09-24 09:54:54', 2, '2018-09-24 09:54:54', 1, 2),
(6, 55, 55, NULL, NULL, NULL, NULL, NULL, NULL, 'no pain ', '2018-10-02 15:03:40', 2, '2018-10-02 15:03:40', NULL, 1),
(7, 51, 51, NULL, NULL, NULL, NULL, NULL, NULL, 'Normal ', '2018-10-04 16:37:07', 2, '2018-10-04 16:37:07', NULL, 1),
(8, 70, 70, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-06 08:48:22', 2, '2018-10-06 08:48:22', NULL, 1),
(9, 72, 72, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-06 09:52:01', 2, '2018-10-06 09:52:01', 2, 2),
(10, 72, 72, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-06 11:09:37', 2, '2018-10-06 11:09:37', NULL, 1),
(11, 75, 75, NULL, NULL, NULL, NULL, NULL, NULL, 'Normal size, no pain, ', '2018-10-11 10:31:44', 2, '2018-10-11 10:31:44', NULL, 1),
(12, 76, 76, NULL, NULL, NULL, NULL, NULL, NULL, 'Normal prostate \r\nNo nodule ', '2018-10-11 14:33:27', 2, '2018-10-11 14:33:27', NULL, 1),
(13, 78, 78, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-12 10:26:14', 2, '2018-10-12 10:26:14', 2, 2),
(14, 78, 78, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-12 10:42:10', 2, '2018-10-12 10:42:10', NULL, 1),
(15, 79, 79, NULL, NULL, NULL, NULL, NULL, NULL, 'Multiple nodules on both lobe ', '2018-10-12 16:13:50', 2, '2018-10-12 16:13:50', NULL, 1),
(16, 85, 85, 'normal', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-13 12:37:35', 2, '2018-10-13 12:37:35', NULL, 1),
(17, 86, 86, 'normal', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-13 16:11:53', 2, '2018-10-13 16:11:53', NULL, 1),
(18, 89, 89, 'normal', 'smooth', NULL, 'obliterated', 'yes', NULL, NULL, '2018-10-14 08:48:36', 2, '2018-10-14 08:48:36', NULL, 1),
(19, 94, 94, 'normal', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-18 13:32:49', 2, '2018-10-18 13:32:49', NULL, 1),
(20, 97, 97, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-19 14:25:45', 2, '2018-10-19 14:25:45', 2, 2),
(21, 97, 97, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-19 14:26:48', 2, '2018-10-19 14:26:48', NULL, 1),
(22, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 09:26:18', 2, '2018-10-20 09:26:18', 1, 2),
(23, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 09:29:32', 2, '2018-10-20 09:29:32', 1, 2),
(24, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 09:32:33', 2, '2018-10-20 09:32:33', 1, 2),
(25, 27, 27, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-20 09:20:37', 9, '2018-10-20 09:20:37', NULL, 1),
(26, 101, 101, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-20 09:24:50', 1, '2018-10-20 09:24:50', 9, 2),
(27, 101, 101, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-20 09:25:56', 9, '2018-10-20 09:25:56', NULL, 1),
(28, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 10:29:23', 2, '2018-10-20 10:29:23', 1, 2),
(29, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 11:04:25', 2, '2018-10-20 11:04:25', 1, 2),
(30, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 11:07:04', 2, '2018-10-20 11:07:04', 1, 2),
(31, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 11:23:25', 2, '2018-10-20 11:23:25', 1, 2),
(32, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2018-10-20 11:24:06', 2, '2018-10-20 11:24:06', 1, 2),
(33, 103, 103, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-20 14:28:01', 2, '2018-10-20 14:28:01', 2, 2),
(34, 103, 103, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-20 14:30:29', 2, '2018-10-20 14:30:29', NULL, 1),
(35, 105, 105, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-21 10:24:14', 1, '2018-10-21 10:24:14', 2, 2),
(36, 105, 105, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-21 10:27:01', 1, '2018-10-21 10:27:01', 2, 2),
(37, 105, 105, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-21 10:32:02', 2, '2018-10-21 10:32:02', 2, 2),
(38, 105, 105, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-21 11:08:46', 2, '2018-10-21 11:08:46', NULL, 1),
(39, 107, 107, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-22 15:14:17', 2, '2018-10-22 15:14:17', 2, 2),
(40, 107, 107, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-22 15:17:06', 2, '2018-10-22 15:17:06', NULL, 1),
(41, 109, 109, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-22 15:56:07', 2, '2018-10-22 15:56:07', NULL, 1),
(42, 108, 108, 'normal', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-22 16:03:08', 2, '2018-10-22 16:03:08', 2, 2),
(43, 108, 108, 'normal', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-22 16:14:57', 2, '2018-10-22 16:14:57', NULL, 1),
(44, 110, 110, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-23 13:27:55', 2, '2018-10-23 13:27:55', 2, 2),
(45, 110, 110, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-23 13:28:35', 2, '2018-10-23 13:28:35', NULL, 1),
(46, 114, 114, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-23 15:44:02', 2, '2018-10-23 15:44:02', NULL, 1),
(47, 119, 119, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-23 16:27:17', 2, '2018-10-23 16:27:17', NULL, 1),
(48, 118, 118, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-23 16:36:37', 2, '2018-10-23 16:36:37', NULL, 1),
(49, 122, 122, 'normal', 'smooth', 'firm', 'obliterated', 'no', NULL, NULL, '2018-10-24 09:10:52', 2, '2018-10-24 09:10:52', NULL, 1),
(50, 123, 123, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-24 15:01:57', 2, '2018-10-24 15:01:57', NULL, 1),
(51, 125, 125, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-24 18:36:32', 2, '2018-10-24 18:36:32', 2, 2),
(52, 125, 125, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-24 19:42:56', 2, '2018-10-24 19:42:56', 2, 2),
(53, 125, 125, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-24 19:45:45', 2, '2018-10-24 19:45:45', 2, 2),
(54, 125, 125, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', NULL, NULL, '2018-10-24 20:04:32', 2, '2018-10-24 20:04:32', NULL, 1),
(55, 129, 129, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-10-25 17:22:08', 2, '2018-10-25 17:22:08', NULL, 1),
(56, 133, 133, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-27 14:15:35', 2, '2018-10-27 14:15:35', NULL, 1),
(57, 134, 134, 'enlarge', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-10-27 15:48:48', 2, '2018-10-27 15:48:48', NULL, 1),
(58, 141, 141, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-29 11:07:29', 2, '2018-10-29 11:07:29', 2, 2),
(59, 141, 141, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-29 11:19:03', 2, '2018-10-29 11:19:03', NULL, 1),
(60, 143, 143, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-29 12:34:19', 2, '2018-10-29 12:34:19', 2, 2),
(61, 143, 143, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-29 13:44:33', 2, '2018-10-29 13:44:33', NULL, 1),
(62, 147, 147, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-10-31 15:31:17', 2, '2018-10-31 15:31:17', NULL, 1),
(63, 150, 150, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-01 14:39:22', 2, '2018-11-01 14:39:22', NULL, 1),
(64, 152, 152, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-03 08:58:02', 2, '2018-11-03 08:58:02', 2, 2),
(65, 152, 152, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-03 09:07:46', 2, '2018-11-03 09:07:46', NULL, 1),
(66, 154, 154, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-04 09:54:55', 2, '2018-11-04 09:54:55', 2, 2),
(67, 155, 155, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-04 10:16:56', 2, '2018-11-04 10:16:56', 2, 2),
(68, 154, 154, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-04 11:21:17', 2, '2018-11-04 11:21:17', NULL, 1),
(69, 157, 157, 'enlarge', 'smooth', 'firm', 'absent', 'no', 'no', NULL, '2018-11-04 12:11:29', 2, '2018-11-04 12:11:29', 2, 2),
(70, 157, 157, 'enlarge', 'smooth', 'firm', 'absent', 'no', 'no', NULL, '2018-11-04 12:12:42', 2, '2018-11-04 12:12:42', NULL, 1),
(71, 155, 155, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-04 12:20:05', 2, '2018-11-04 12:20:05', NULL, 1),
(72, 160, 160, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-05 15:00:39', 2, '2018-11-05 15:00:39', 2, 2),
(73, 160, 160, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-05 15:13:03', 2, '2018-11-05 15:13:03', 2, 2),
(74, 160, 160, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-05 16:44:08', 2, '2018-11-05 16:44:08', NULL, 1),
(75, 166, 166, 'normal', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-11-05 17:02:04', 2, '2018-11-05 17:02:04', 2, 2),
(76, 166, 166, 'normal', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-11-05 18:12:38', 2, '2018-11-05 18:12:38', NULL, 1),
(77, 169, 169, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-06 15:26:15', 2, '2018-11-06 15:26:15', NULL, 1),
(78, 171, 171, NULL, NULL, NULL, NULL, NULL, NULL, 'Both Testicles: Normal volume. no pain ', '2018-11-07 08:25:14', 2, '2018-11-07 08:25:14', 2, 2),
(79, 171, 171, NULL, NULL, NULL, NULL, NULL, NULL, 'Both Testicles: Normal volume. no pain ', '2018-11-07 08:25:51', 2, '2018-11-07 08:25:51', NULL, 1),
(80, 174, 174, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-07 18:57:46', 2, '2018-11-07 18:57:46', NULL, 1),
(81, 178, 178, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-08 17:32:56', 2, '2018-11-08 17:32:56', NULL, 1),
(82, 179, 179, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-08 18:17:54', 2, '2018-11-08 18:17:54', NULL, 1),
(83, 180, 180, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 10:43:01', 2, '2018-11-09 10:43:01', 2, 2),
(84, 180, 180, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 10:43:10', 2, '2018-11-09 10:43:10', 2, 2),
(85, 180, 180, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 12:13:59', 2, '2018-11-09 12:13:59', NULL, 1),
(86, 181, 181, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 14:04:34', 2, '2018-11-09 14:04:34', 2, 2),
(87, 181, 181, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 15:29:31', 2, '2018-11-09 15:29:31', 2, 2),
(88, 181, 181, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-09 15:45:57', 2, '2018-11-09 15:45:57', NULL, 1),
(89, 183, 183, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-10 12:05:49', 2, '2018-11-10 12:05:49', 2, 2),
(90, 183, 183, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-10 12:07:05', 2, '2018-11-10 12:07:05', NULL, 1),
(91, 184, 184, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-11 08:23:52', 2, '2018-11-11 08:23:52', 2, 2),
(92, 184, 184, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', '', '2018-11-11 08:30:02', 2, '2018-11-11 08:30:02', 2, 2),
(93, 184, 184, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-11 08:30:57', 2, '2018-11-11 08:30:57', 2, 2),
(94, 184, 184, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-11 08:35:20', 2, '2018-11-11 08:35:20', NULL, 1),
(95, 188, 188, 'normal', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-11-11 13:00:43', 2, '2018-11-11 13:00:43', 2, 2),
(96, 188, 188, 'normal', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-11-11 13:02:13', 2, '2018-11-11 13:02:13', 2, 2),
(97, 189, 189, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-11 14:06:28', 2, '2018-11-11 14:06:28', NULL, 1),
(98, 191, 191, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-11 14:33:52', 2, '2018-11-11 14:33:52', 2, 2),
(99, 191, 191, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-11 14:34:59', 2, '2018-11-11 14:34:59', 2, 2),
(100, 191, 191, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-11 15:08:43', 2, '2018-11-11 15:08:43', NULL, 1),
(101, 188, 188, 'normal', 'smooth', 'firm', 'obliterated', 'yes', 'no', NULL, '2018-11-11 17:32:35', 2, '2018-11-11 17:32:35', NULL, 1),
(102, 192, 192, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 11:42:16', 2, '2018-11-12 11:42:16', 2, 2),
(103, 192, 192, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 11:42:45', 2, '2018-11-12 11:42:45', NULL, 1),
(104, 194, 194, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 14:18:01', 2, '2018-11-12 14:18:01', 2, 2),
(105, 194, 194, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 14:21:32', 2, '2018-11-12 14:21:32', NULL, 1),
(106, 196, 196, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-12 15:10:34', 2, '2018-11-12 15:10:34', NULL, 1),
(107, 195, 195, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-12 15:54:01', 2, '2018-11-12 15:54:01', NULL, 1),
(108, 200, 200, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-12 17:05:12', 2, '2018-11-12 17:05:12', NULL, 1),
(109, 202, 202, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 17:19:18', 2, '2018-11-12 17:19:18', 2, 2),
(110, 202, 202, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-12 17:19:29', 2, '2018-11-12 17:19:29', NULL, 1),
(111, 203, 203, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-12 17:33:21', 2, '2018-11-12 17:33:21', NULL, 1),
(112, 207, 207, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-13 17:21:50', 2, '2018-11-13 17:21:50', 2, 2),
(113, 207, 207, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-13 17:36:14', 2, '2018-11-13 17:36:14', 2, 2),
(114, 206, 206, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-13 17:59:54', 2, '2018-11-13 17:59:54', 2, 2),
(115, 206, 206, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-13 18:00:03', 2, '2018-11-13 18:00:03', NULL, 1),
(116, 207, 207, 'enlarge', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-13 18:59:30', 2, '2018-11-13 18:59:30', NULL, 1),
(117, 209, 209, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-14 13:00:34', 2, '2018-11-14 13:00:34', 2, 2),
(118, 209, 209, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-14 13:01:59', 2, '2018-11-14 13:01:59', NULL, 1),
(119, 201, 201, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-14 16:10:42', 2, '2018-11-14 16:10:42', NULL, 1),
(120, 213, 213, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-14 16:39:00', 2, '2018-11-14 16:39:00', 2, 2),
(121, 213, 213, 'normal', NULL, 'firm', 'obliterated', 'no', 'no', '', '2018-11-14 18:55:14', 2, '2018-11-14 18:55:14', NULL, 1),
(122, 217, 217, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 11:09:31', 2, '2018-11-15 11:09:31', 2, 2),
(123, 217, 217, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 11:17:26', 2, '2018-11-15 11:17:26', 2, 2),
(124, 217, 217, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 12:12:45', 2, '2018-11-15 12:12:45', NULL, 1),
(125, 218, 218, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 14:42:46', 2, '2018-11-15 14:42:46', 2, 2),
(126, 220, 220, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 15:49:05', 2, '2018-11-15 15:49:05', 2, 2),
(127, 218, 218, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 16:35:53', 2, '2018-11-15 16:35:53', NULL, 1),
(128, 220, 220, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 16:43:24', 2, '2018-11-15 16:43:24', 2, 2),
(129, 222, 222, NULL, NULL, NULL, NULL, NULL, NULL, '', '2018-11-15 17:15:29', 2, '2018-11-15 17:15:29', NULL, 1),
(130, 220, 220, 'normal', 'smooth', 'firm', 'obliterated', 'no', 'no', NULL, '2018-11-15 17:26:53', 2, '2018-11-15 17:26:53', NULL, 1),
(131, 20, 20, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-02-21 15:40:00', 1, '2019-02-21 15:40:00', NULL, 1),
(132, 26, 26, NULL, NULL, NULL, NULL, NULL, NULL, 'Normal ', '2019-02-25 09:16:46', 1, '2019-02-25 09:16:46', NULL, 1),
(133, 31, 31, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-01 09:38:10', 1, '2019-03-01 09:38:10', NULL, 1),
(134, 42, 42, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 09:07:26', 1, '2019-03-05 09:07:26', 1, 2),
(135, 42, 42, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 09:07:58', 1, '2019-03-05 09:07:58', 1, 2),
(136, 42, 42, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 09:45:21', 1, '2019-03-05 09:45:21', 1, 2),
(137, 42, 42, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 10:02:09', 1, '2019-03-05 10:02:09', 1, 2),
(138, 45, 45, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 14:42:30', 1, '2019-03-05 14:42:30', 1, 2),
(139, 45, 45, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 14:42:37', 1, '2019-03-05 14:42:37', 1, 2),
(140, 45, 45, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 14:42:39', 1, '2019-03-05 14:42:39', 1, 2),
(141, 45, 45, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 14:42:39', 1, '2019-03-05 14:42:39', 1, 2),
(142, 45, 45, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 14:42:39', 1, '2019-03-05 14:42:39', NULL, 1),
(143, 42, 42, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-05 16:38:53', 1, '2019-03-05 16:38:53', NULL, 1),
(144, 65, 65, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-12 15:18:29', 1, '2019-03-12 15:18:29', 1, 2),
(145, 65, 65, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-12 15:18:29', 1, '2019-03-12 15:18:29', 1, 2),
(146, 65, 65, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-12 15:18:32', 1, '2019-03-12 15:18:32', 1, 2),
(147, 65, 65, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-12 15:18:32', 1, '2019-03-12 15:18:32', NULL, 1),
(148, 93, 93, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-17 13:48:52', 1, '2019-03-17 13:48:52', 1, 2),
(149, 93, 93, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-17 13:49:51', 1, '2019-03-17 13:49:51', 1, 2),
(150, 93, 93, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-17 13:50:41', 1, '2019-03-17 13:50:41', 1, 2),
(151, 93, 93, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-17 13:50:42', 1, '2019-03-17 13:50:42', 1, 2),
(152, 93, 93, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-17 17:14:09', 1, '2019-03-17 17:14:09', NULL, 1),
(153, 98, 98, NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-03-19 16:36:53', 1, '2019-03-19 16:36:53', NULL, 1),
(154, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2019-03-20 10:25:27', 1, '2019-03-20 10:25:27', 1, 2),
(155, 100, 100, 'enlarge', 'smooth', NULL, 'obliterated', 'no', NULL, NULL, '2019-03-20 10:25:40', 1, '2019-03-20 10:25:40', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `price` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `sys_code`, `name`, `type`, `price`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '231341411233', 'Admin', NULL, NULL, '2017-02-17 09:31:52', 1, '2023-05-31 15:41:30', 1, 1),
(2, '9a386278fd59477b67008b4e44b7ff77', 'OPD Doctors', NULL, NULL, '2017-12-23 10:46:34', 1, '2023-02-21 13:27:52', 1, 1),
(3, 'b25e41d5789b25e3d7c4f966412cc434', ' Cashier + Pharmacy + Inventory', NULL, NULL, '2018-09-14 10:11:45', 8, '2019-08-05 17:39:36', 14, 1),
(4, '0be25c96cf5a77341b941a48dd3d3504', 'Nurse Group', NULL, NULL, '2018-09-14 10:14:35', 1, '2019-08-05 17:39:07', 14, 1),
(5, '85f81ca47cf280781edaffda830fe9d3', 'Registration', NULL, NULL, '2019-01-08 12:05:51', 1, '2019-01-08 12:05:51', NULL, 1),
(6, '2305bc594503d17ae182ba8dbfa898a7', 'Stock Inventory', NULL, NULL, '2019-01-08 13:14:29', 1, '2019-05-06 14:51:09', 1, 1),
(7, '0c94bc3e96503eccd77be877f361d0d1', 'Labo', NULL, NULL, '2019-01-19 08:27:15', 1, '2019-08-05 17:40:25', 14, 1),
(8, '194d5e7acfb4877beecd51514aa83b3c', 'Labo Entry', NULL, NULL, '2019-03-11 11:59:53', 11, '2019-08-05 17:40:38', 14, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_insurances`
--

CREATE TABLE IF NOT EXISTS `group_insurances` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `group_insurances`
--

INSERT INTO `group_insurances` (`id`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Company', 'for their staff/staff family only', '2016-07-21 12:33:20', 1, '2016-07-21 12:33:20', NULL, 1),
(2, 'Local Insurance', 'located in Cambodia', '2016-07-21 12:33:42', 1, '2016-07-21 12:33:42', NULL, 1),
(3, 'International Insurance', 'Located outside Cambodia', '2016-07-21 12:34:03', 1, '2016-07-21 12:34:03', NULL, 1),
(4, 'Pheakdey', '', '2018-01-29 14:40:50', 1, '2018-01-29 14:40:50', NULL, 1),
(5, 'B', 'test', '2019-05-03 16:46:31', 1, '2019-05-03 16:46:42', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `indications`
--

CREATE TABLE IF NOT EXISTS `indications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:is_active 2:edit 3:delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `indications`
--

INSERT INTO `indications` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Example Indication', '2018-03-10 11:49:33', 1, '2018-03-10 11:49:33', 1, 2),
(2, 'Example Indication', '2019-05-03 16:19:08', 1, '2019-05-03 16:19:08', NULL, 1),
(3, 'Example', '2019-05-03 16:19:24', 1, '2019-05-03 16:19:24', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE IF NOT EXISTS `inventories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint(20) DEFAULT NULL,
  `consignment_return_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_id` bigint(20) DEFAULT NULL,
  `vendor_consignment_return_id` bigint(20) DEFAULT NULL,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `cycle_product_detail_id` bigint(20) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `point_of_sales_id` bigint(20) DEFAULT NULL,
  `credit_memo_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `purchase_return_id` bigint(20) DEFAULT NULL,
  `transfer_order_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_group_id` int(11) NOT NULL,
  `qty` decimal(15,3) NOT NULL,
  `unit_cost` decimal(18,9) DEFAULT '0.000000000',
  `unit_price` decimal(15,4) DEFAULT '0.0000',
  `date` date NOT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `date_expired` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` bigint(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` bigint(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `location_id` (`location_id`),
  KEY `lots_number` (`lots_number`),
  KEY `qty` (`qty`),
  KEY `location_group_id` (`location_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `inventories`
--
DROP TRIGGER IF EXISTS `zInventoriesAfterInsert`;
DELIMITER //
CREATE TRIGGER `zInventoriesAfterInsert` AFTER INSERT ON `inventories`
 FOR EACH ROW BEGIN
	DECLARE qty decimal(15,3);
	DECLARE totalQty decimal(15,3);
	DECLARE totalAmtSales decimal(15,3);
	DECLARE totalAmtPurchase decimal(15,3);
   SELECT SUM((total_pb + total_cm + total_cycle) - (total_so + total_pos + total_pbc)) INTO totalQty FROM inventory_total_by_dates WHERE product_id = NEW.product_id AND lots_number = NEW.lots_number AND expired_date = NEW.date_expired AND date <= NEW.date;
	IF totalQty IS NULL THEN SET totalQty = 0; END IF;
	IF NEW.type = 'Inv Adj' THEN 
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_cycle`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_cycle = (total_cycle + qty), total_ending = totalQty;
	ELSEIF NEW.type = 'Purchase' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtPurchase = ROUND(NEW.qty * NEW.unit_cost, 2);
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_purchase`, `total_pb`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtPurchase, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pb = (total_pb + qty), total_amount_purchase = (total_amount_purchase + totalAmtPurchase), total_ending = totalQty;
   ELSEIF NEW.type = 'Void Purchase' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtPurchase = ROUND(NEW.qty * NEW.unit_cost, 2) * -1;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_purchase`, `total_pb`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtPurchase, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pb = (total_pb + qty), total_amount_purchase = (total_amount_purchase + totalAmtPurchase), total_ending = totalQty;
	ELSEIF NEW.type = 'Purchase Return' THEN
		SET qty  = (NEW.qty * -1);
		SET totalQty =  totalQty - qty;
		SET totalAmtPurchase = ROUND((NEW.qty * NEW.unit_cost), 2);
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_purchase`, `total_pbc`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtPurchase, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pbc = (total_pbc + qty), total_amount_purchase = (total_amount_purchase + totalAmtPurchase), total_ending = totalQty;
	ELSEIF NEW.type = 'Void Purchase Return' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtPurchase = ROUND((NEW.qty * NEW.unit_cost), 2);
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_purchase`, `total_pbc`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtPurchase, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pbc = (total_pbc - qty), total_amount_purchase = (total_amount_purchase + totalAmtPurchase), total_ending = totalQty;
	ELSEIF NEW.type = 'POS' THEN
		SET qty  = (NEW.qty * -1);
		SET totalQty =  totalQty - qty;
		SET totalAmtSales = NEW.unit_price;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_pos`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pos = (total_pos + qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
   ELSEIF NEW.type = 'Void POS' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtSales = NEW.unit_price * -1;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_pos`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_pos = (total_pos - qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
	ELSEIF NEW.type = 'Sale' THEN
		SET qty  = (NEW.qty * -1);
		SET totalQty =  totalQty - qty;
		SET totalAmtSales = NEW.unit_price;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_so`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_so = (total_so + qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
	ELSEIF NEW.type = 'Void Sale' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtSales = NEW.unit_price * -1;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_so`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_so = (total_so - qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
	ELSEIF NEW.type = 'Sales Return' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtSales = NEW.unit_price * -1;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_cm`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_cm = (total_cm + qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
   ELSEIF NEW.type = 'Void Sales Return' THEN
		SET qty  = NEW.qty;
		SET totalQty =  totalQty + qty;
		SET totalAmtSales = NEW.unit_price;
		INSERT INTO inventory_total_by_dates (`product_id`, `lots_number`, `expired_date`, `date`, `total_amount_sales`, `total_cm`, `total_ending`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.date, totalAmtSales, qty, totalQty) 
		ON DUPLICATE KEY UPDATE total_cm = (total_cm + qty), total_amount_sales = (total_amount_sales + totalAmtSales), total_ending = totalQty;
	END IF;
	
	INSERT INTO product_inventories (`product_id`, `lots_number`, `expired_date`, `location_group_id`, `location_id`, `total_qty`) VALUES (NEW.product_id, NEW.lots_number, NEW.date_expired, NEW.location_group_id, NEW.location_id, NEW.qty) 
	ON DUPLICATE KEY UPDATE total_qty = (total_qty + NEW.qty);
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_physicals`
--

CREATE TABLE IF NOT EXISTS `inventory_physicals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`location_group_id`),
  KEY `searchs` (`code`,`date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_physical_crontabs`
--

CREATE TABLE IF NOT EXISTS `inventory_physical_crontabs` (
  `inventory_physical_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  UNIQUE KEY `inventory_physical_id` (`inventory_physical_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_physical_details`
--

CREATE TABLE IF NOT EXISTS `inventory_physical_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_physical_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty_diff` decimal(15,3) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `expired_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_mix_id` (`inventory_physical_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_totals`
--

CREATE TABLE IF NOT EXISTS `inventory_totals` (
  `product_id` double NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_so_free` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pos_free` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_cm_free` decimal(15,3) DEFAULT '0.000',
  `total_to_in` decimal(15,3) DEFAULT '0.000',
  `total_to_out` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_cus_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_in` decimal(15,3) DEFAULT '0.000',
  `total_ven_consign_out` decimal(15,3) DEFAULT '0.000',
  `total_order` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`lots_number`,`expired_date`),
  KEY `product_id` (`product_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_total_by_dates`
--

CREATE TABLE IF NOT EXISTS `inventory_total_by_dates` (
  `product_id` double NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `total_amount_sales` decimal(20,9) DEFAULT '0.000000000',
  `total_amount_purchase` decimal(20,9) DEFAULT '0.000000000',
  `total_cycle` decimal(15,3) DEFAULT '0.000',
  `total_so` decimal(15,3) DEFAULT '0.000',
  `total_pos` decimal(15,3) DEFAULT '0.000',
  `total_pb` decimal(15,3) DEFAULT '0.000',
  `total_pbc` decimal(15,3) DEFAULT '0.000',
  `total_cm` decimal(15,3) DEFAULT '0.000',
  `total_ending` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`product_id`,`lots_number`,`expired_date`,`date`,`type`),
  KEY `index_searchs` (`product_id`,`lots_number`,`expired_date`,`date`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_unit_costs`
--

CREATE TABLE IF NOT EXISTS `inventory_unit_costs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `total_qty` decimal(15,3) DEFAULT '0.000',
  `unit_cost` decimal(18,9) DEFAULT '0.000000000' COMMENT 'Main UOM',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_valuations`
--

CREATE TABLE IF NOT EXISTS `inventory_valuations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `cycle_product_id` bigint(20) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `credit_memo_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `purchase_order_detail_id` bigint(20) DEFAULT NULL,
  `purchase_return_id` bigint(20) DEFAULT NULL,
  `point_of_sales_id` bigint(20) DEFAULT NULL,
  `inventory_physical_id` bigint(20) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `small_qty` decimal(15,3) DEFAULT NULL,
  `qty` decimal(15,9) DEFAULT NULL COMMENT 'Main QTY',
  `cost` decimal(18,9) DEFAULT NULL COMMENT 'Main Cost Uom',
  `price` decimal(18,9) DEFAULT NULL,
  `on_hand` decimal(20,9) DEFAULT NULL,
  `on_hand_small` decimal(20,9) DEFAULT NULL,
  `avg_cost` decimal(20,9) DEFAULT NULL COMMENT 'Main Cost Uom',
  `asset_value` decimal(20,9) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `date_edited` datetime DEFAULT NULL,
  `is_var_cost` tinyint(4) DEFAULT '0',
  `is_adjust_value` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `searchs` (`date`,`pid`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `queue_id` bigint(20) DEFAULT NULL,
  `ar_id` int(11) DEFAULT NULL,
  `invoice_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_amount` double DEFAULT '0',
  `total_discount` double DEFAULT '0',
  `balance` double DEFAULT '0',
  `exchange_rate_id` int(11) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_convert` int(11) DEFAULT '0' COMMENT '0:not yet convert; 1: convert',
  `is_void` tinyint(4) DEFAULT '0',
  `type_payment_id` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `invoice_code` (`invoice_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE IF NOT EXISTS `invoice_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT '1' COMMENT '1 is service, 2 is labo, 3 is pharmacy',
  `date_created` date DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `discount` double DEFAULT '0',
  `unit_price` double DEFAULT '0',
  `hospital_price` double DEFAULT '0',
  `total_price` double DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_pbc_with_pbs`
--

CREATE TABLE IF NOT EXISTS `invoice_pbc_with_pbs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_id` int(10) DEFAULT NULL,
  `purchase_return_id` int(10) DEFAULT NULL,
  `total_cost` decimal(15,3) DEFAULT '0.000',
  `apply_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_By` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `purchase_return_id` (`purchase_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labos`
--

CREATE TABLE IF NOT EXISTS `labos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queued_id` int(11) DEFAULT NULL,
  `number_lab` varchar(50) DEFAULT NULL,
  `labo_site_id` int(11) DEFAULT NULL,
  `doctor_id` varchar(100) DEFAULT NULL,
  `diagonist` text,
  `chief_complain` text,
  `file` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `validate` datetime DEFAULT NULL,
  `validate_by` int(11) DEFAULT NULL,
  `is_validate` tinyint(4) DEFAULT '0' COMMENT '0 not yet validate; 1 validated',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_anapaths`
--

CREATE TABLE IF NOT EXISTS `labo_anapaths` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queued_patient_id` bigint(20) NOT NULL,
  `doctor_name` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `ngo_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `queued_patient_id` (`queued_patient_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_anapath_requests`
--

CREATE TABLE IF NOT EXISTS `labo_anapath_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `labo_anapath_id` bigint(20) DEFAULT NULL,
  `anapath_id` bigint(20) DEFAULT NULL,
  `result` text COLLATE utf8_unicode_ci,
  `information` mediumtext COLLATE utf8_unicode_ci,
  `exament_macroscopy` longtext COLLATE utf8_unicode_ci,
  `exament_microscopy` longtext COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `conclusion` longtext COLLATE utf8_unicode_ci,
  `surgery_date` date DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_files`
--

CREATE TABLE IF NOT EXISTS `labo_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `labo_id` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `labo_files`
--

INSERT INTO `labo_files` (`id`, `labo_id`, `file`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 1009, '3789621ac3a45bf9826868aa086fba87_6.pdf', '2022-12-15 18:13:00', 10, '2022-12-15 18:13:20', 10, 2),
(2, 1009, '3789621ac3a45bf9826868aa086fba87_4.pdf', '2022-12-15 18:13:00', 10, '2022-12-15 18:13:17', 10, 2),
(3, 1009, '46d8d7304e4e566d9a1b1154eb40d480_14.pdf', '2022-12-15 18:13:28', 10, '2022-12-15 18:13:28', NULL, 1),
(4, 1009, '46d8d7304e4e566d9a1b1154eb40d480_11.pdf', '2022-12-15 18:13:28', 10, '2022-12-15 18:13:28', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_items`
--

CREATE TABLE IF NOT EXISTS `labo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` int(255) DEFAULT NULL,
  `title_item` int(11) DEFAULT '0',
  `normal_value_type` varchar(50) DEFAULT NULL,
  `normal_value_m` varchar(50) DEFAULT NULL,
  `min_value_m` varchar(50) DEFAULT NULL,
  `max_value_m` varchar(50) DEFAULT NULL,
  `normal_value_f` varchar(50) DEFAULT NULL,
  `min_value_f` varchar(50) DEFAULT NULL,
  `max_value_f` varchar(50) DEFAULT NULL,
  `description` text,
  `item_unit` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(3) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

--
-- Dumping data for table `labo_items`
--

INSERT INTO `labo_items` (`id`, `parent_id`, `name`, `category`, `title_item`, `normal_value_type`, `normal_value_m`, `min_value_m`, `max_value_m`, `normal_value_f`, `min_value_f`, `max_value_f`, `description`, `item_unit`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, NULL, 'WBC/Globules Blance', 1, 1, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '10⁹/L', '2019-05-01 13:27:48', 10, '2019-05-07 11:25:08', 10, 1),
(2, NULL, 'RBC/Globules Rouge', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '10^12/L', '2019-05-01 14:02:50', 10, '2020-02-16 14:38:31', 10, 1),
(3, NULL, 'Hemogolbin/HGB', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'g/dl', '2019-05-01 14:03:49', 10, '2019-05-07 11:38:05', 10, 1),
(4, NULL, 'Hematocrite/HCT', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:04:31', 10, '2019-05-07 11:39:56', 10, 1),
(5, NULL, 'MCV/VGM', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'fL', '2019-05-01 14:05:14', 10, '2019-05-07 11:59:49', 10, 1),
(6, NULL, 'MCH/CCMH', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'pg', '2019-05-01 14:06:14', 10, '2019-05-07 12:02:28', 10, 1),
(7, NULL, 'MCHC/TCMH', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'g/dl', '2019-05-01 14:06:55', 10, '2019-05-07 12:06:40', 10, 1),
(8, NULL, 'PLT/Plaquettes', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '10⁹/L', '2019-05-01 14:07:59', 10, '2019-05-07 12:09:07', 10, 1),
(9, NULL, 'P.Neutrophil(5eqs)', 1, 2, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:08:57', 10, '2019-05-01 14:08:57', NULL, 1),
(10, NULL, 'P.Eosinophil', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:09:58', 10, '2019-05-01 14:09:58', NULL, 1),
(11, NULL, 'P.Basophil', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:12:04', 10, '2019-05-01 14:12:04', NULL, 1),
(12, NULL, 'Lymphocytes', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:12:34', 10, '2019-05-01 14:12:34', NULL, 1),
(13, NULL, 'Monocytes', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '%', '2019-05-01 14:13:26', 10, '2019-05-01 14:13:26', NULL, 1),
(14, NULL, 'ABO Group, Rh', 1, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 14:20:38', 10, '2019-05-01 14:20:38', NULL, 1),
(15, NULL, 'ESR/VS', 1, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mm/h', '2019-05-01 14:27:37', 10, '2019-05-01 14:27:37', NULL, 1),
(16, NULL, 'BUN/Uree', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-01 15:15:04', 10, '2019-05-07 14:49:25', 10, 1),
(17, NULL, 'Creatinine', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-01 15:22:25', 10, '2019-05-07 14:41:52', 10, 1),
(18, NULL, 'CRP(Quantitative)', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/L', '2019-05-01 15:26:52', 10, '2019-05-07 14:52:05', 10, 1),
(19, NULL, 'Gamma-GT', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'U/L', '2019-05-01 15:28:45', 10, '2019-05-07 15:01:36', 10, 1),
(20, NULL, 'Glycemia', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-01 15:30:54', 10, '2019-05-07 14:50:03', 10, 1),
(21, NULL, 'Transaminases-SGOT(AST)', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'U/L', '2019-05-01 15:32:03', 10, '2019-05-07 15:01:10', 10, 1),
(22, NULL, 'HBs-Ag (RDT)', 3, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:37:00', 10, '2019-05-04 09:13:15', 11, 1),
(23, NULL, 'Anti-HBs (RDT)', 3, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:38:27', 10, '2019-05-01 15:38:27', NULL, 1),
(24, NULL, 'Anti-HCV (RDT)', 3, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:41:56', 10, '2019-05-01 15:41:56', NULL, 1),
(25, NULL, 'HIV (Ab/Ag) Combi', 3, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:43:55', 10, '2019-05-01 15:43:55', NULL, 1),
(26, NULL, 'Rotavirus Ag', 3, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:48:39', 10, '2019-05-01 15:48:39', NULL, 1),
(27, NULL, 'Dengue fever(NS1,Ag)', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:53:22', 10, '2019-05-08 16:41:04', 10, 1),
(28, NULL, 'Dengue fever(IgG)', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:53:42', 10, '2019-05-08 16:40:55', 10, 1),
(29, NULL, 'Dengue fever(IgM)', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 15:54:05', 10, '2019-05-08 16:40:44', 10, 1),
(30, NULL, 'EV 71 IgM', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 16:27:41', 10, '2019-05-01 16:27:41', NULL, 1),
(31, NULL, 'Research of Antigen-Influenza A', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 16:31:44', 10, '2019-05-01 16:31:44', NULL, 1),
(32, NULL, 'Research of Antigen-Influenza B', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 16:31:58', 10, '2019-05-01 16:31:58', NULL, 1),
(33, NULL, 'Urine Cytology', 6, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 16:55:18', 10, '2019-05-01 16:55:18', NULL, 1),
(34, NULL, 'Urine Dipstick', 6, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-01 16:56:02', 10, '2019-05-01 16:56:02', NULL, 1),
(35, NULL, 'Na+', 7, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mmol/l', '2019-05-07 09:28:41', 10, '2019-05-07 14:42:21', 10, 1),
(36, NULL, 'K+', 7, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mmol/l', '2019-05-07 09:30:22', 10, '2019-05-07 14:42:48', 10, 1),
(37, NULL, 'Chloride', 7, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mmol/l', '2019-05-07 09:36:28', 10, '2019-05-07 14:43:12', 10, 1),
(38, NULL, 'Cholesterol Total', 2, 3, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-07 09:52:07', 10, '2019-05-07 15:37:49', 10, 1),
(39, NULL, 'Cholesterol HDL', 8, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-07 09:53:02', 10, '2020-06-01 13:51:41', 10, 1),
(40, NULL, 'Cholesterol LDL', 8, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-07 09:53:54', 10, '2020-06-01 13:53:10', 10, 1),
(41, NULL, 'Triglycerides', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-07 09:55:41', 10, '2019-05-07 15:38:53', 10, 1),
(42, NULL, 'Transaminases-SGPT(ALT)', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'U/L', '2019-05-07 15:04:25', 10, '2019-05-30 18:02:44', 10, 1),
(43, NULL, 'Uric Acid', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-07 15:19:23', 10, '2019-05-07 15:33:54', 10, 1),
(44, NULL, 'Dengue IgG', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-08 14:55:57', 10, '2019-05-08 15:51:33', 10, 2),
(45, NULL, 'Dengue IgM', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-08 14:59:52', 10, '2019-05-08 15:51:53', 10, 2),
(46, NULL, 'Dengue NS1 Ag', 5, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-08 15:00:12', 10, '2019-05-08 15:52:13', 10, 2),
(47, NULL, 'Strep A Ag', 3, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-08 18:54:11', 10, '2019-05-09 08:06:05', 10, 1),
(48, NULL, 'Aspect', 6, 4, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 11:55:48', 10, '2019-05-09 15:28:24', 10, 1),
(49, NULL, 'Color', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 11:57:29', 10, '2019-05-09 11:57:29', NULL, 1),
(50, NULL, 'Red Blood Cell', 6, 5, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'cell/hpf', '2019-05-09 12:07:41', 10, '2019-12-06 15:34:12', 10, 1),
(51, NULL, 'White Blood Cell', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'cell/hpf', '2019-05-09 12:08:56', 10, '2019-12-06 15:36:03', 10, 1),
(52, NULL, 'Cell-ephithelial', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'cell/hpf', '2019-05-09 12:10:50', 10, '2019-12-06 16:05:29', 10, 1),
(53, NULL, 'Cell-Renal tubular', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:12:48', 10, '2019-12-06 16:05:59', 10, 1),
(54, NULL, 'Cast/Cyline', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:14:11', 10, '2019-12-06 16:06:28', 10, 1),
(55, NULL, 'Crystal', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', 'cell/hpf', '2019-05-09 12:20:19', 10, '2019-05-09 12:20:19', NULL, 1),
(56, NULL, 'Yeast/Levure', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:21:26', 10, '2019-12-06 16:07:36', 10, 1),
(57, NULL, 'Leucocytes', 6, 6, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:25:47', 10, '2019-12-06 16:10:25', 10, 1),
(58, NULL, 'Billirubin', 6, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:29:15', 10, '2019-12-06 16:10:36', 10, 1),
(59, NULL, 'Urobilinogen', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2019-05-09 12:31:08', 10, '2019-05-09 12:31:08', NULL, 1),
(60, NULL, 'Ketones', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:32:19', 10, '2019-05-09 12:32:19', NULL, 1),
(61, NULL, 'Protein', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:33:04', 10, '2019-12-06 16:19:44', 10, 1),
(62, NULL, 'Nitrite', 6, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:33:41', 10, '2019-12-06 16:09:19', 10, 1),
(63, NULL, 'Glucose', 6, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:34:07', 10, '2019-12-06 16:09:35', 10, 1),
(64, NULL, 'pH', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:34:42', 10, '2019-05-09 12:34:42', NULL, 1),
(65, NULL, 'Specific gravity', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-09 12:41:32', 10, '2019-05-09 12:41:32', NULL, 1),
(66, NULL, 'Salmonella typhi IgM (RDT)', 3, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-11 15:18:32', 10, '2019-05-11 15:28:19', 10, 1),
(67, NULL, 'H.pylori (IgM+IgG)', 3, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-05-27 13:15:37', 10, '2019-05-27 13:15:37', NULL, 1),
(68, NULL, 'Malaria Ag P.f/Pan (SD)', 3, NULL, 'Positive / Negative', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-07-07 11:52:53', 10, '2019-07-07 11:52:53', NULL, 1),
(69, NULL, 'Hbs-Ag (Quantitative)', 5, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mIU/ml', '2019-08-21 17:09:33', 10, '2019-08-22 08:44:22', 10, 1),
(70, NULL, 'Anti-Hbs (Quantitative)', 5, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mIU/ml', '2019-08-21 17:22:35', 10, '2019-08-22 08:40:39', 10, 1),
(71, NULL, 'HbA1c', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2019-12-08 09:11:32', 10, '2019-12-08 09:26:39', 10, 1),
(72, NULL, 'Microalbumin', 6, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/L', '2020-01-03 14:48:08', 10, '2020-01-03 14:48:08', NULL, 1),
(73, NULL, 'C3', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2020-01-07 12:02:19', 10, '2020-01-07 12:05:33', 10, 1),
(74, NULL, 'C4', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2020-01-07 12:09:12', 10, '2020-01-07 12:09:12', NULL, 1),
(75, NULL, 'Anti-dsDNA', 5, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'U/mL', '2020-01-07 12:12:53', 10, '2020-01-07 12:12:53', NULL, 1),
(76, NULL, 'Urine Examination', 6, NULL, 'Free Style', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2020-03-03 08:03:02', 10, '2020-03-03 08:03:02', NULL, 1),
(77, NULL, 'TSH', 9, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'µUl/mL', '2020-05-08 10:43:55', 10, '2020-05-08 10:43:55', NULL, 1),
(78, NULL, 'FT3', 9, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'pg/ml', '2020-05-08 10:46:33', 10, '2020-05-08 10:46:33', NULL, 1),
(79, NULL, 'FT4', 9, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'pg/ml', '2020-05-08 10:48:12', 10, '2020-05-08 10:48:12', NULL, 1),
(80, NULL, 'CEA', 10, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, 'CEA: High CEA concentrations are frequently found in cases of colorectal adenocarcinoma. Slight to moderate CEA elevations (rarely >10 ng/mL) occur in 20% to 50% of benign diseases of the intestine, pancreas, liver, and lungs (eg, liver cirrhosis, chronic hepatitis, pancreatitis, ulcerative colitis, Crohn''s disease, emphysema). Smokers also have elevated CEA values. Results cannot be interpreted as absolute evidence of the presence or absence of malignant disease.', 'ng/ml', '2020-06-23 11:37:55', 10, '2020-06-24 15:05:28', 10, 1),
(81, NULL, 'Bicarbonates', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mmol/l', '2020-06-23 11:44:37', 10, '2020-06-23 14:29:54', 10, 1),
(82, NULL, 'eGFR MDRD (Caucasian)', 11, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mL/min/1.73m2', '2020-06-23 13:43:14', 10, '2020-06-23 21:39:12', 10, 1),
(83, NULL, 'eGFR (CKD-EPI)', 11, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatinine: * Clinical Practice guidelines for chronic kidney disease.CKD stage1: ≥ 90 with proteinuria. CKD\nStage2: eGFR: 60 - 89 with proteinuria. Most of the time an eGFR > 59, means the kidneys are healthy and working\nwell. CKD stage3: eGFR: 30 - 59. Stage 3 kidney disease means the kidneys are moderately damaged and are not\nworking as well as they should. CKD stage4: eGFR: 15 - 29,Stage 4 kidney disease means your kidneys are\nmoderately or severely damaged. CKD stage5: eGFR <15 Stage 5 kidney disease means the kidneys are getting very\nclose to failure or have completely failed.\n', 'mL/min/1.73m2', '2020-06-23 13:45:13', 10, '2020-06-23 21:42:19', 10, 1),
(84, NULL, 'ALP/PAL', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'U/L', '2020-06-23 13:52:02', 10, '2020-06-23 13:52:02', NULL, 1),
(85, NULL, 'Protein Total', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'g/dl', '2020-06-23 13:55:37', 10, '2020-06-23 13:55:37', NULL, 1),
(86, NULL, 'Bilirubin Direct(Conjugated)', 12, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2020-06-23 13:59:17', 10, '2020-06-23 13:59:17', NULL, 1),
(87, NULL, 'Bilirubin Total', 12, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'mg/dL', '2020-06-23 14:00:43', 10, '2020-06-23 14:00:43', NULL, 1),
(88, NULL, 'Albumin', 2, NULL, 'Number', NULL, NULL, NULL, NULL, NULL, NULL, '', 'g/dl', '2020-06-23 15:28:16', 10, '2020-06-23 15:28:16', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_categories`
--

CREATE TABLE IF NOT EXISTS `labo_item_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `labo_item_categories`
--

INSERT INTO `labo_item_categories` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'HEMATOLOGY', '2019-05-01 13:18:52', 10, '2019-05-01 13:18:52', NULL, 1),
(2, 'BIOCHEMISTRY', '2019-05-01 13:19:00', 10, '2019-05-01 13:19:00', NULL, 1),
(3, '( RAPID DIAGNOSITC TEST)', '2019-05-01 13:19:11', 10, '2019-05-01 13:19:26', 10, 1),
(4, 'INFECTION', '2019-05-01 13:19:35', 10, '2019-05-01 13:19:35', NULL, 1),
(5, 'SERO-IMMUNOLOGY', '2019-05-01 13:19:47', 10, '2019-05-01 13:19:47', NULL, 1),
(6, 'URINE', '2019-05-01 13:19:55', 10, '2019-05-01 13:19:55', NULL, 1),
(7, 'IONOGRAME', '2019-05-07 09:16:27', 10, '2019-05-07 15:36:57', 10, 1),
(8, 'LIPID PROFILE', '2019-05-07 09:49:37', 10, '2019-05-07 09:49:37', NULL, 1),
(9, 'HORMONOLOGY', '2020-05-08 10:21:00', 10, '2020-05-08 10:49:09', 10, 1),
(10, 'TUMOR MARKER', '2020-06-23 11:33:04', 10, '2020-06-23 11:33:04', NULL, 1),
(11, 'eGFR', '2020-06-23 12:26:58', 10, '2020-06-24 14:46:06', 10, 1),
(12, 'BILIRUBIN', '2020-06-23 13:58:06', 10, '2020-06-23 13:58:06', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_details`
--

CREATE TABLE IF NOT EXISTS `labo_item_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_item_id` int(11) DEFAULT NULL,
  `age_for_labo_id` int(11) DEFAULT NULL,
  `min_value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `labo_item_id` (`labo_item_id`),
  KEY `age_for_labo_id` (`age_for_labo_id`),
  KEY `age_for_labo_id_2` (`age_for_labo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=213 ;

--
-- Dumping data for table `labo_item_details`
--

INSERT INTO `labo_item_details` (`id`, `labo_item_id`, `age_for_labo_id`, `min_value`, `max_value`, `created`, `created_by`, `status`) VALUES
(1, 1, 1, '6.00', '16.00', '2019-05-01 13:27:48', 10, 0),
(2, 1, 8, '6.00', '16.00', '2019-05-01 13:27:48', 10, 0),
(3, 2, 1, '3.9', '5.3', '2019-05-01 14:02:50', 10, 0),
(4, 2, 8, '3.9', '5.3', '2019-05-01 14:02:50', 10, 0),
(5, 3, 1, '11.0', '14.0', '2019-05-01 14:03:49', 10, 0),
(6, 3, 8, '11.0', '14.0', '2019-05-01 14:03:49', 10, 0),
(7, 4, 1, '30', '40', '2019-05-01 14:04:31', 10, 0),
(8, 4, 8, '30', '40', '2019-05-01 14:04:31', 10, 0),
(9, 5, 1, '68.0', '87.0', '2019-05-01 14:05:14', 10, 0),
(10, 5, 8, '68.0', '87.0', '2019-05-01 14:05:14', 10, 0),
(11, 6, 1, '24.0', '30.0', '2019-05-01 14:06:14', 10, 0),
(12, 6, 8, '24.0', '30.0', '2019-05-01 14:06:14', 10, 0),
(13, 7, 1, '29.0', '37.0', '2019-05-01 14:06:55', 10, 0),
(14, 7, 8, '29.0', '37.0', '2019-05-01 14:06:55', 10, 0),
(15, 8, 1, '200', '500', '2019-05-01 14:07:59', 10, 0),
(16, 8, 8, '200', '500', '2019-05-01 14:07:59', 10, 0),
(17, 9, 1, '36', '66', '2019-05-01 14:08:57', 10, 1),
(18, 9, 8, '36', '66', '2019-05-01 14:08:57', 10, 1),
(19, 10, 1, '02', '05', '2019-05-01 14:09:58', 10, 1),
(20, 10, 8, '02', '05', '2019-05-01 14:09:58', 10, 1),
(21, 11, 1, '00', '01', '2019-05-01 14:12:04', 10, 1),
(22, 11, 8, '00', '01', '2019-05-01 14:12:04', 10, 1),
(23, 12, 1, '25', '45', '2019-05-01 14:12:34', 10, 1),
(24, 12, 8, '25', '45', '2019-05-01 14:12:34', 10, 1),
(25, 13, 1, '03', '10', '2019-05-01 14:13:26', 10, 1),
(26, 13, 8, '03', '10', '2019-05-01 14:13:26', 10, 1),
(27, 15, 1, '0', '20', '2019-05-01 14:27:37', 10, 1),
(28, 16, 1, '3.2', '7.3', '2019-05-01 15:15:04', 10, 0),
(29, 17, 1, '44', '106', '2019-05-01 15:22:25', 10, 0),
(30, 18, 1, '00', '5', '2019-05-01 15:26:52', 10, 0),
(31, 19, 1, '8', '49', '2019-05-01 15:28:45', 10, 0),
(32, 19, 8, '7', '26', '2019-05-01 15:28:45', 10, 0),
(33, 20, 1, '70', '115', '2019-05-01 15:30:54', 10, 0),
(34, 21, 1, '0', '42', '2019-05-01 15:32:03', 10, 0),
(35, 35, 1, '135', '148', '2019-05-07 09:39:47', 10, 0),
(36, 36, 1, '3.5', '5.2', '2019-05-07 09:40:12', 10, 0),
(37, 37, 1, '97', '107', '2019-05-07 09:40:36', 10, 0),
(38, 38, 1, '<200', '<200', '2019-05-07 09:52:07', 10, 0),
(39, 39, 1, '35', '150', '2019-05-07 09:53:02', 10, 0),
(40, 40, 1, '0', '<130', '2019-05-07 09:53:54', 10, 0),
(41, 41, 1, '60', '160', '2019-05-07 09:55:41', 10, 0),
(42, 38, 1, '<200', '<200', '2019-05-07 09:56:45', 10, 0),
(43, 41, 1, '60', '160', '2019-05-07 09:56:59', 10, 0),
(44, 1, 1, '6.00', '16.00', '2019-05-07 11:18:01', 10, 0),
(45, 1, 2, '5.00', '13.00', '2019-05-07 11:18:01', 10, 0),
(46, 1, 3, '5.00', '13.00', '2019-05-07 11:18:01', 10, 0),
(47, 1, 4, '5.00', '13.00', '2019-05-07 11:18:01', 10, 0),
(48, 1, 5, '4.00', '11.00', '2019-05-07 11:18:01', 10, 0),
(49, 1, 6, '4.00', '11.00', '2019-05-07 11:18:01', 10, 0),
(50, 1, 7, '6.00', '16.00', '2019-05-07 11:18:01', 10, 0),
(51, 1, 8, '6.00', '16.00', '2019-05-07 11:18:01', 10, 0),
(52, 2, 1, '3.90', '5.30', '2019-05-07 11:23:55', 10, 0),
(53, 2, 2, '3.90', '5.30', '2019-05-07 11:23:55', 10, 0),
(54, 2, 3, '4.00', '5.20', '2019-05-07 11:23:55', 10, 0),
(55, 2, 4, '4.00', '5.20', '2019-05-07 11:23:55', 10, 0),
(56, 2, 5, '4.60', '6.00', '2019-05-07 11:23:55', 10, 0),
(57, 2, 6, '3.90', '5.50', '2019-05-07 11:23:55', 10, 0),
(58, 2, 7, '3.90', '5.30', '2019-05-07 11:23:55', 10, 0),
(59, 2, 8, '3.90', '5.30', '2019-05-07 11:23:55', 10, 0),
(60, 1, 1, '6.00', '16.00', '2019-05-07 11:25:09', 10, 1),
(61, 1, 2, '6.00', '16.00', '2019-05-07 11:25:09', 10, 1),
(62, 1, 3, '5.00', '13.00', '2019-05-07 11:25:09', 10, 1),
(63, 1, 4, '5.00', '13.00', '2019-05-07 11:25:09', 10, 1),
(64, 1, 5, '4.00', '11.00', '2019-05-07 11:25:09', 10, 1),
(65, 1, 6, '4.00', '11.00', '2019-05-07 11:25:09', 10, 1),
(66, 1, 7, '6.00', '16.00', '2019-05-07 11:25:09', 10, 1),
(67, 1, 8, '6.00', '16.00', '2019-05-07 11:25:09', 10, 1),
(68, 3, 1, '11.0', '14.0', '2019-05-07 11:38:05', 10, 1),
(69, 3, 2, '11.0', '14.0', '2019-05-07 11:38:05', 10, 1),
(70, 3, 3, '11.5', '15.5', '2019-05-07 11:38:05', 10, 1),
(71, 3, 4, '11.5', '15.5', '2019-05-07 11:38:05', 10, 1),
(72, 3, 5, '13.0', '17.0', '2019-05-07 11:38:05', 10, 1),
(73, 3, 6, '12.0', '15.0', '2019-05-07 11:38:06', 10, 1),
(74, 3, 7, '11.0', '14.0', '2019-05-07 11:38:06', 10, 1),
(75, 3, 8, '11.0', '14.0', '2019-05-07 11:38:06', 10, 1),
(76, 4, 1, '30', '40', '2019-05-07 11:39:56', 10, 1),
(77, 4, 2, '30', '40', '2019-05-07 11:39:56', 10, 1),
(78, 4, 3, '35', '45', '2019-05-07 11:39:56', 10, 1),
(79, 4, 4, '35', '45', '2019-05-07 11:39:56', 10, 1),
(80, 4, 5, '40', '50', '2019-05-07 11:39:56', 10, 1),
(81, 4, 6, '36', '46', '2019-05-07 11:39:56', 10, 1),
(82, 4, 7, '30', '40', '2019-05-07 11:39:56', 10, 1),
(83, 4, 8, '30', '40', '2019-05-07 11:39:57', 10, 1),
(84, 5, 1, '68.0', '87.0', '2019-05-07 11:59:49', 10, 1),
(85, 5, 2, '68.0', '87.0', '2019-05-07 11:59:49', 10, 1),
(86, 5, 3, '80.0', '95.0', '2019-05-07 11:59:49', 10, 1),
(87, 5, 4, '80.0', '95.0', '2019-05-07 11:59:49', 10, 1),
(88, 5, 5, '80.0', '98.0', '2019-05-07 11:59:49', 10, 1),
(89, 5, 6, '80.0', '98.0', '2019-05-07 11:59:49', 10, 1),
(90, 5, 7, '68.0', '87.0', '2019-05-07 11:59:49', 10, 1),
(91, 5, 8, '68.0', '87.0', '2019-05-07 11:59:49', 10, 1),
(92, 6, 1, '24.0', '30.0', '2019-05-07 12:02:28', 10, 1),
(93, 6, 2, '24.0', '30.0', '2019-05-07 12:02:28', 10, 1),
(94, 6, 3, '25.0', '35.0', '2019-05-07 12:02:28', 10, 1),
(95, 6, 4, '25.0', '35.0', '2019-05-07 12:02:28', 10, 1),
(96, 6, 5, '25.0', '35.0', '2019-05-07 12:02:28', 10, 1),
(97, 6, 6, '25.0', '35.0', '2019-05-07 12:02:29', 10, 1),
(98, 6, 7, '24.0', '30', '2019-05-07 12:02:29', 10, 1),
(99, 6, 8, '24.0', '30.0', '2019-05-07 12:02:29', 10, 1),
(100, 7, 1, '29.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(101, 7, 2, '29.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(102, 7, 3, '30.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(103, 7, 4, '30.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(104, 7, 5, '32.0', '36.0', '2019-05-07 12:06:40', 10, 1),
(105, 7, 6, '32.0', '36.0', '2019-05-07 12:06:40', 10, 1),
(106, 7, 7, '29.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(107, 7, 8, '29.0', '37.0', '2019-05-07 12:06:40', 10, 1),
(108, 8, 1, '200', '500', '2019-05-07 12:09:07', 10, 1),
(109, 8, 2, '200', '500', '2019-05-07 12:09:07', 10, 1),
(110, 8, 3, '150', '400', '2019-05-07 12:09:07', 10, 1),
(111, 8, 4, '150', '400', '2019-05-07 12:09:07', 10, 1),
(112, 8, 5, '150', '400', '2019-05-07 12:09:07', 10, 1),
(113, 8, 6, '150', '400', '2019-05-07 12:09:07', 10, 1),
(114, 8, 7, '200', '500', '2019-05-07 12:09:07', 10, 1),
(115, 8, 8, '200', '500', '2019-05-07 12:09:08', 10, 1),
(116, 17, 1, '0.5', '1.1', '2019-05-07 14:41:53', 10, 1),
(117, 35, 1, '135', '148', '2019-05-07 14:42:21', 10, 1),
(118, 36, 1, '3.5', '5.2', '2019-05-07 14:42:48', 10, 1),
(119, 37, 1, '97', '107', '2019-05-07 14:43:12', 10, 1),
(120, 19, 3, '11', '61', '2019-05-07 14:48:31', 10, 0),
(121, 19, 4, '09', '39', '2019-05-07 14:48:31', 10, 0),
(122, 19, 5, '11', '61', '2019-05-07 14:48:31', 10, 0),
(123, 19, 6, '09', '39', '2019-05-07 14:48:31', 10, 0),
(124, 19, 7, '09', '39', '2019-05-07 14:48:31', 10, 0),
(125, 19, 8, '11', '61', '2019-05-07 14:48:31', 10, 0),
(126, 16, 1, '10', '50', '2019-05-07 14:49:25', 10, 1),
(127, 20, 1, '75', '115', '2019-05-07 14:50:03', 10, 1),
(128, 18, 1, '00', '6', '2019-05-07 14:52:05', 10, 1),
(129, 21, 2, '00', '38', '2019-05-07 15:01:10', 10, 1),
(130, 21, 3, '00', '38', '2019-05-07 15:01:10', 10, 1),
(131, 21, 4, '00', '35', '2019-05-07 15:01:10', 10, 1),
(132, 21, 5, '00', '38', '2019-05-07 15:01:10', 10, 1),
(133, 21, 6, '00', '35', '2019-05-07 15:01:10', 10, 1),
(134, 21, 7, '00', '35', '2019-05-07 15:01:10', 10, 1),
(135, 21, 8, '00', '38', '2019-05-07 15:01:10', 10, 1),
(136, 19, 3, '11', '61', '2019-05-07 15:01:36', 10, 1),
(137, 19, 4, '09', '39', '2019-05-07 15:01:36', 10, 1),
(138, 19, 5, '11', '61', '2019-05-07 15:01:36', 10, 1),
(139, 19, 6, '09', '39', '2019-05-07 15:01:36', 10, 1),
(140, 19, 7, '09', '39', '2019-05-07 15:01:36', 10, 1),
(141, 19, 8, '11', '61', '2019-05-07 15:01:36', 10, 1),
(142, 39, 1, '35', '150', '2019-05-07 15:07:27', 10, 0),
(143, 41, 1, '', '<200', '2019-05-07 15:08:57', 10, 0),
(144, 39, 1, '', '>35', '2019-05-07 15:17:14', 10, 0),
(145, 41, 1, '', '<150', '2019-05-07 15:25:48', 10, 0),
(146, 43, 2, '2.4', '7.0', '2019-05-07 15:33:54', 10, 1),
(147, 43, 3, '3.4', '7.0', '2019-05-07 15:33:54', 10, 1),
(148, 43, 4, '2.4', '5.7', '2019-05-07 15:33:54', 10, 1),
(149, 43, 5, '3.4', '7.0', '2019-05-07 15:33:54', 10, 1),
(150, 43, 6, '2.4', '5.7', '2019-05-07 15:33:54', 10, 1),
(151, 43, 7, '2.4', '5.7', '2019-05-07 15:33:54', 10, 1),
(152, 43, 8, '3.4', '7.0', '2019-05-07 15:33:54', 10, 1),
(153, 38, 1, '', '<200', '2019-05-07 15:37:50', 10, 1),
(154, 40, 1, '', '<150', '2019-05-07 15:38:12', 10, 0),
(155, 39, 1, '', '>35', '2019-05-07 15:38:30', 10, 0),
(156, 41, 1, '', '<150', '2019-05-07 15:38:53', 10, 1),
(157, 59, 1, '0.1', '1.0', '2019-05-09 12:31:08', 10, 1),
(158, 64, 1, '5.0', '8.0', '2019-05-09 12:34:42', 10, 1),
(159, 65, 1, '1.003', '1.035', '2019-05-09 12:41:32', 10, 1),
(160, 42, 1, '', '<50', '2019-05-30 17:59:23', 10, 0),
(161, 42, 5, '', '<50', '2019-05-30 17:59:23', 10, 0),
(162, 42, 6, '', '<35', '2019-05-30 17:59:23', 10, 0),
(163, 42, 5, '00', '50', '2019-05-30 18:02:44', 10, 1),
(164, 42, 6, '00', '35', '2019-05-30 18:02:44', 10, 1),
(165, 70, 1, 'Negative: <10', '', '2019-08-22 08:40:39', 10, 1),
(166, 69, 1, 'Negative: <10', '', '2019-08-22 08:42:04', 10, 0),
(167, 69, 1, 'Negative: <10', '', '2019-08-22 08:44:22', 10, 1),
(168, 50, 1, '<2', '', '2019-12-06 15:34:12', 10, 1),
(169, 51, 1, '<2', '', '2019-12-06 15:36:03', 10, 1),
(170, 52, 1, '1', '10', '2019-12-06 15:38:16', 10, 0),
(171, 53, 1, 'Not found', '', '2019-12-06 15:42:06', 10, 0),
(172, 54, 1, 'Not found', '', '2019-12-06 15:43:15', 10, 0),
(173, 56, 1, 'Not found', '', '2019-12-06 15:43:49', 10, 0),
(174, 57, 1, 'Negative', '', '2019-12-06 15:45:20', 10, 0),
(175, 58, 1, 'Negative', '', '2019-12-06 15:46:09', 10, 0),
(176, 57, 1, 'Negative', '', '2019-12-06 15:48:00', 10, 0),
(177, 61, 1, 'Negative', '', '2019-12-06 15:48:31', 10, 0),
(178, 62, 1, 'Negative', '', '2019-12-06 15:48:56', 10, 0),
(179, 63, 1, 'Negative', '', '2019-12-06 15:49:18', 10, 0),
(180, 52, 1, '<10', '', '2019-12-06 16:05:29', 10, 1),
(181, 71, 1, '<7%', '', '2019-12-08 09:11:32', 10, 0),
(182, 71, 1, '<7%', '', '2019-12-08 09:26:40', 10, 1),
(183, 72, 1, '<18', '', '2020-01-03 14:48:08', 10, 1),
(184, 73, 1, '80', '170', '2020-01-07 12:02:19', 10, 0),
(185, 73, 1, '80', '170', '2020-01-07 12:05:33', 10, 1),
(186, 74, 1, '15', '45', '2020-01-07 12:09:13', 10, 1),
(187, 75, 1, '<25', '', '2020-01-07 12:12:53', 10, 1),
(188, 2, 1, '3.90', '5.30', '2020-02-16 14:38:31', 10, 1),
(189, 2, 2, '3.90', '5.30', '2020-02-16 14:38:31', 10, 1),
(190, 2, 3, '4.00', '5.20', '2020-02-16 14:38:31', 10, 1),
(191, 2, 4, '4.00', '5.20', '2020-02-16 14:38:31', 10, 1),
(192, 2, 5, '4.60', '6.00', '2020-02-16 14:38:31', 10, 1),
(193, 2, 6, '3.90', '5.50', '2020-02-16 14:38:31', 10, 1),
(194, 2, 7, '3.90', '5.30', '2020-02-16 14:38:32', 10, 1),
(195, 2, 8, '3.90', '5.30', '2020-02-16 14:38:32', 10, 1),
(196, 77, 1, '0.30', '4.50', '2020-05-08 10:43:55', 10, 1),
(197, 78, 1, '2.00', '4.40', '2020-05-08 10:46:33', 10, 1),
(198, 79, 1, '8.2', '17.7', '2020-05-08 10:48:12', 10, 1),
(199, 39, 1, '', '>39', '2020-06-01 13:51:41', 10, 1),
(200, 40, 1, '', '<99', '2020-06-01 13:53:10', 10, 1),
(201, 80, 1, '<5', '', '2020-06-23 11:37:55', 10, 0),
(202, 81, 1, '17', '29', '2020-06-23 11:44:37', 10, 0),
(203, 81, 1, '17', '29', '2020-06-23 11:46:36', 10, 0),
(204, 84, 1, '0', '270', '2020-06-23 13:52:02', 10, 1),
(205, 85, 1, '6.6', '8.7', '2020-06-23 13:55:37', 10, 1),
(206, 86, 1, '<0.25', '', '2020-06-23 13:59:17', 10, 1),
(207, 87, 1, '<1.0', '', '2020-06-23 14:00:43', 10, 1),
(208, 81, 1, '17', '29', '2020-06-23 14:29:54', 10, 1),
(209, 80, 1, '<5', '', '2020-06-23 14:48:10', 10, 0),
(210, 88, 1, '3.8', '5.5', '2020-06-23 15:28:16', 10, 1),
(211, 80, 1, '<5', '', '2020-06-23 21:25:11', 10, 0),
(212, 80, 1, '<5', '', '2020-06-24 15:05:28', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_groups`
--

CREATE TABLE IF NOT EXISTS `labo_item_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `chart_account_id_expense` int(11) DEFAULT NULL,
  `count` double DEFAULT NULL,
  `labo_item_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `labo_sub_title_group_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `price` double DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=52 ;

--
-- Dumping data for table `labo_item_groups`
--

INSERT INTO `labo_item_groups` (`id`, `company_id`, `chart_account_id`, `chart_account_id_expense`, `count`, `labo_item_id`, `labo_sub_title_group_id`, `code`, `name`, `price`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 1, 99, NULL, NULL, '4, 3, 12, 6, 7, 5, 13, 11, 10, 9, 8, 2, 1', NULL, '0001', 'Hg (CBC)', NULL, '2019-05-01 14:16:24', 10, '2019-05-07 09:43:05', 10, 1),
(2, 1, 99, NULL, NULL, '14', NULL, '0002', 'ABO Group, Rh', NULL, '2019-05-01 14:22:08', 10, '2019-05-01 15:19:50', 10, 1),
(3, 1, 99, NULL, NULL, '15', NULL, '0003', 'ESR (VS)', NULL, '2019-05-01 14:28:08', 10, '2019-05-07 09:43:59', 10, 1),
(4, 1, 99, NULL, NULL, '16', NULL, '0004', 'BUN/Uree', NULL, '2019-05-01 15:16:57', 10, '2019-05-01 15:16:57', NULL, 1),
(5, 1, 99, NULL, NULL, '17', NULL, '0005', 'Creatinine', NULL, '2019-05-01 15:22:55', 10, '2019-05-01 15:22:55', NULL, 1),
(6, 1, 99, NULL, NULL, '18', NULL, '0006', 'CRP', NULL, '2019-05-01 15:27:35', 10, '2019-05-07 09:44:56', 10, 1),
(7, 1, 99, NULL, NULL, '19', NULL, '0007', 'Gamma-GT', NULL, '2019-05-01 15:29:15', 10, '2019-05-01 15:29:15', NULL, 1),
(8, 1, 99, NULL, NULL, '20', NULL, '0008', 'Glycemia', NULL, '2019-05-01 15:31:17', 10, '2019-05-01 15:31:17', NULL, 1),
(9, 1, 99, NULL, NULL, '21, 42', NULL, '0009', 'Transaminases', NULL, '2019-05-01 15:32:44', 10, '2019-05-07 15:30:57', 10, 1),
(10, 1, 99, NULL, NULL, '22', NULL, '0010', 'HBs-Ag (RDT)', NULL, '2019-05-01 15:37:47', 10, '2019-05-08 15:39:47', 10, 1),
(11, 1, 99, NULL, NULL, '23', NULL, '0011', 'Anti-HBs (RDT)', NULL, '2019-05-01 15:40:57', 10, '2019-05-08 15:40:05', 10, 1),
(12, 1, 99, NULL, NULL, '24', NULL, '0012', 'Anti-HCV (RDT)', NULL, '2019-05-01 15:43:02', 10, '2019-05-08 15:40:21', 10, 1),
(13, 1, 99, NULL, NULL, '25', NULL, '0013', 'HIV (Ab/Ag) Combi', NULL, '2019-05-01 15:44:30', 10, '2019-05-08 15:40:56', 10, 1),
(14, 1, 99, NULL, NULL, '26', NULL, '0014', 'Rotavirus Ag', NULL, '2019-05-01 15:49:16', 10, '2019-05-08 15:41:19', 10, 1),
(15, 1, 99, NULL, NULL, '28, 29, 27', NULL, '0015', 'Dengue (NS1Ag, IgM, IgG)', NULL, '2019-05-01 15:54:54', 10, '2019-05-16 16:13:49', 10, 1),
(16, 1, 99, NULL, NULL, '30', NULL, '0016', 'EV 71 IgM', NULL, '2019-05-01 16:30:23', 10, '2019-05-08 15:41:47', 10, 1),
(17, 1, 99, NULL, NULL, '31, 32', NULL, '0017', 'Influenza Ag (A,B)', NULL, '2019-05-01 16:34:34', 10, '2019-05-08 15:42:14', 10, 1),
(18, 1, 99, NULL, NULL, '48, 54, 52, 53, 49, 55, 50, 51, 56', NULL, '0018', 'Urine Cytology', NULL, '2019-05-01 16:56:59', 10, '2019-05-09 15:31:27', 10, 1),
(19, 1, 99, NULL, NULL, '58, 63, 60, 57, 62, 64, 61, 50, 65, 59', NULL, '0019', 'Urine Dipstick', NULL, '2019-05-01 16:57:48', 10, '2019-05-09 15:36:01', 10, 1),
(20, 1, 99, NULL, NULL, '37, 36, 35', NULL, '0020', 'ionograme (Na, K, Cl)', NULL, '2019-05-04 09:19:40', 11, '2019-05-07 09:45:44', 10, 1),
(21, 1, 99, NULL, NULL, '38', NULL, '0021', 'Cholesterol Total', NULL, '2019-05-07 09:57:47', 10, '2019-05-07 09:57:47', NULL, 1),
(22, 1, 99, NULL, NULL, '39', NULL, '0022', 'Cholesterol HDL', NULL, '2019-05-07 09:58:44', 10, '2019-05-07 09:58:44', NULL, 1),
(23, 1, 99, NULL, NULL, '40', NULL, '0022', 'Cholesterol LDL', NULL, '2019-05-07 10:46:03', 10, '2019-05-07 10:46:03', NULL, 1),
(24, 1, 99, NULL, NULL, '41', NULL, '0023', 'Triglycerides', NULL, '2019-05-07 10:47:47', 10, '2019-05-07 10:47:47', NULL, 1),
(25, 1, 99, NULL, NULL, '43', NULL, '0024', 'Uric Acid', NULL, '2019-05-07 15:35:19', 10, '2019-08-15 10:39:52', 10, 1),
(26, 1, 99, NULL, NULL, '44', NULL, '0025', 'Gangue', NULL, '2019-05-08 14:56:35', 10, '2019-05-08 14:56:35', 10, 2),
(27, 1, 99, NULL, NULL, '47', NULL, '0025', 'Strep A Ag', NULL, '2019-05-09 08:05:19', 10, '2019-05-09 08:05:19', NULL, 1),
(28, 1, 99, NULL, NULL, '66', NULL, '0026', 'Salmonella typhi IgM (RDT)', NULL, '2019-05-11 15:30:19', 10, '2019-05-11 15:30:19', NULL, 1),
(29, 1, 99, NULL, NULL, '67', NULL, '0027', 'H.pylori (IgM+IgG)', NULL, '2019-05-27 13:22:37', 10, '2019-05-27 13:22:37', NULL, 1),
(30, 1, 99, NULL, NULL, '4, 3, 12, 6, 7, 5, 13, 11, 10, 9, 8, 2, 1', NULL, '0028', 'Hg (CBC) 1', NULL, '2019-06-27 13:38:08', 7, '2019-06-27 13:38:08', NULL, 1),
(31, 1, 99, NULL, NULL, '4, 3, 12, 6, 7, 5, 13, 11, 10, 9, 8, 2, 1', NULL, '0029', 'Hg (CBC) 2', NULL, '2019-06-27 13:39:39', 7, '2019-06-27 13:39:39', NULL, 1),
(32, 1, 99, NULL, NULL, '4, 3, 12, 6, 7, 5, 13, 11, 10, 9, 8, 2, 1', NULL, '0030', 'Hg (CBC)3', NULL, '2019-06-27 13:41:05', 7, '2019-06-27 13:41:05', NULL, 1),
(33, 1, 99, NULL, NULL, '68', NULL, '0031', 'Malaria Ag P.f/Pan (SD)', NULL, '2019-07-07 11:57:36', 10, '2019-08-21 17:16:19', 10, 1),
(34, 1, 99, NULL, NULL, '69', NULL, '0032', 'Hbs-Ag (Quantitative)', NULL, '2019-08-21 17:13:54', 10, '2019-08-22 08:30:44', 10, 1),
(35, 1, 99, NULL, NULL, '70', NULL, '0033', 'Anti-Hbs (Quantitative)', NULL, '2019-08-21 17:24:18', 10, '2019-08-22 08:31:34', 10, 1),
(36, 1, 99, NULL, NULL, '71', NULL, '0034', 'HbA1c', NULL, '2019-12-08 09:18:36', 10, '2019-12-08 09:25:43', 10, 1),
(37, 1, 99, NULL, NULL, '72', NULL, '0035', 'Microalbumin', NULL, '2020-01-03 15:53:27', 10, '2020-01-03 15:53:27', NULL, 1),
(38, 1, 99, NULL, NULL, '73', NULL, '0036', 'C3', NULL, '2020-01-07 12:05:08', 10, '2020-01-07 12:05:08', NULL, 1),
(39, 1, 99, NULL, NULL, '74', NULL, '0037', 'C4', NULL, '2020-01-07 12:10:26', 10, '2020-01-07 12:10:26', NULL, 1),
(40, 1, 99, NULL, NULL, '75', NULL, '0038', 'Anti-dsDNA', NULL, '2020-01-07 12:14:38', 10, '2020-01-07 12:14:38', NULL, 1),
(41, 1, 99, NULL, NULL, '48, 58, 54, 52, 53, 49, 55, 63, 60, 57, 62, 61, 50, 65, 59, 51, 56, 64', NULL, '0039', 'Urine Examination', NULL, '2020-01-16 14:32:16', 10, '2020-03-09 15:20:17', 10, 1),
(42, 1, 99, NULL, NULL, '77', NULL, '0040', 'TSH', NULL, '2020-05-08 10:57:25', 10, '2020-05-08 14:21:23', 10, 1),
(43, 1, 99, NULL, NULL, '78', NULL, '0041', 'FT3', NULL, '2020-05-08 11:04:01', 10, '2020-05-08 11:06:35', 10, 1),
(44, 1, 99, NULL, NULL, '79', NULL, '0042', 'FT4', NULL, '2020-05-08 11:04:52', 10, '2020-05-08 11:07:27', 10, 1),
(45, 1, 99, NULL, NULL, '80', NULL, '0043', 'CEA', NULL, '2020-06-23 11:40:23', 10, '2020-06-23 14:51:36', 10, 1),
(46, 1, 99, NULL, NULL, '81', NULL, '0044', 'Bicarbonates', NULL, '2020-06-23 11:48:26', 10, '2020-06-23 11:48:26', NULL, 1),
(47, 1, 99, NULL, NULL, '83, 82', NULL, '0045', 'eGFR', NULL, '2020-06-23 13:49:08', 10, '2020-06-24 14:47:27', 10, 1),
(48, 1, 99, NULL, NULL, '84', NULL, '0046', 'ALP/PAL', NULL, '2020-06-23 13:53:19', 10, '2020-06-23 14:21:44', 10, 1),
(49, 1, 99, NULL, NULL, '85', NULL, '0047', 'Protein Total', NULL, '2020-06-23 13:56:37', 10, '2020-06-23 15:10:17', 10, 1),
(50, 1, 99, NULL, NULL, '86, 87', NULL, '0048', 'BILIRUBIN', NULL, '2020-06-23 14:02:19', 10, '2020-06-23 14:02:19', NULL, 1),
(51, 1, 99, NULL, NULL, '88', NULL, '0049', 'Albumin', NULL, '2020-06-23 15:30:10', 10, '2020-06-23 15:30:10', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_patient_groups`
--

CREATE TABLE IF NOT EXISTS `labo_item_patient_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_item_group_id` int(11) DEFAULT NULL,
  `patient_group_id` int(11) DEFAULT NULL,
  `unit_price` float DEFAULT '0',
  `hospital_price` float DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=197 ;

--
-- Dumping data for table `labo_item_patient_groups`
--

INSERT INTO `labo_item_patient_groups` (`id`, `labo_item_group_id`, `patient_group_id`, `unit_price`, `hospital_price`, `is_active`) VALUES
(1, 1, 1, 3, 0, 2),
(2, 1, 2, 3, 0, 2),
(3, 2, 1, 2, 0, 2),
(4, 2, 2, 2, 0, 2),
(5, 3, 1, 2, 0, 2),
(6, 3, 2, 2, 0, 2),
(7, 4, 1, 2, 1, 1),
(8, 4, 2, 2, 1, 1),
(9, 1, 1, 3, 1.5, 2),
(10, 1, 2, 3, 1.5, 2),
(11, 2, 1, 2, 1, 1),
(12, 2, 2, 2, 1, 1),
(13, 3, 1, 2, 0.75, 2),
(14, 3, 2, 2, 0.75, 2),
(15, 5, 1, 2, 1, 1),
(16, 5, 2, 2, 1, 1),
(17, 6, 1, 3, 1.75, 2),
(18, 6, 2, 3, 1.75, 2),
(19, 7, 1, 2, 1, 1),
(20, 7, 2, 2, 1, 1),
(21, 8, 1, 2, 1, 1),
(22, 8, 2, 2, 1, 1),
(23, 9, 1, 2, 1.25, 2),
(24, 9, 2, 2, 1.25, 2),
(25, 10, 1, 5, 2, 2),
(26, 10, 2, 5, 2, 2),
(27, 11, 1, 5, 2, 2),
(28, 11, 2, 5, 2, 2),
(29, 12, 1, 5, 2, 2),
(30, 12, 2, 5, 2, 2),
(31, 13, 1, 8, 4.55, 2),
(32, 13, 2, 8, 4.55, 2),
(33, 14, 1, 12, 8, 2),
(34, 14, 2, 12, 8, 2),
(35, 15, 1, 15, 8, 2),
(36, 15, 2, 15, 8, 2),
(37, 16, 1, 15, 8, 2),
(38, 16, 2, 15, 8, 2),
(39, 17, 1, 15, 9, 2),
(40, 17, 2, 15, 9, 2),
(41, 18, 1, 3, 1.5, 2),
(42, 18, 2, 3, 1.5, 2),
(43, 19, 1, 3, 1.5, 2),
(44, 19, 2, 3, 1.5, 2),
(45, 1, 1, 3, 1.5, 2),
(46, 1, 2, 3, 1.5, 2),
(47, 20, 1, 10, 5, 2),
(48, 20, 2, 10, 5, 2),
(49, 20, 1, 10, 6, 2),
(50, 20, 2, 10, 6, 2),
(51, 20, 1, 10, 6, 2),
(52, 20, 2, 10, 6, 2),
(53, 1, 1, 4, 2, 1),
(54, 1, 2, 4, 2, 1),
(55, 3, 1, 2, 1, 1),
(56, 3, 2, 2, 1, 1),
(57, 6, 1, 4, 2, 1),
(58, 6, 2, 4, 2, 1),
(59, 20, 1, 12, 6, 1),
(60, 20, 2, 12, 6, 1),
(61, 9, 1, 3, 1.5, 2),
(62, 9, 2, 3, 1.5, 2),
(63, 21, 1, 2, 1, 1),
(64, 21, 2, 2, 1, 1),
(65, 22, 1, 3, 1.5, 1),
(66, 22, 2, 3, 1.5, 1),
(67, 23, 1, 4, 2, 1),
(68, 23, 2, 4, 2, 1),
(69, 24, 1, 2, 1, 1),
(70, 24, 2, 2, 1, 1),
(71, 9, 1, 3, 1.5, 2),
(72, 9, 2, 3, 1.5, 2),
(73, 9, 1, 3, 1.5, 1),
(74, 9, 2, 3, 1.5, 1),
(75, 25, 1, 2, 1, 2),
(76, 25, 2, 2, 1, 2),
(77, 26, 1, 16, 8, 1),
(78, 26, 2, 16, 8, 1),
(79, 15, 1, 16, 8, 2),
(80, 15, 2, 16, 8, 2),
(81, 10, 1, 4, 2, 1),
(82, 10, 2, 4, 2, 1),
(83, 11, 1, 4, 2, 1),
(84, 11, 2, 4, 2, 1),
(85, 12, 1, 4, 2, 1),
(86, 12, 2, 4, 2, 1),
(87, 13, 1, 9, 4.5, 1),
(88, 13, 2, 9, 4.5, 1),
(89, 14, 1, 16, 8, 1),
(90, 14, 2, 16, 8, 1),
(91, 16, 1, 16, 8, 1),
(92, 16, 2, 16, 8, 1),
(93, 17, 1, 18, 9, 1),
(94, 17, 2, 18, 9, 1),
(95, 27, 1, 12, 6, 1),
(96, 27, 2, 12, 6, 1),
(97, 18, 1, 3, 1.5, 2),
(98, 18, 2, 3, 1.5, 2),
(99, 19, 1, 3, 1.5, 2),
(100, 19, 2, 3, 1.5, 2),
(101, 18, 1, 3, 1.5, 1),
(102, 18, 2, 3, 1.5, 1),
(103, 19, 1, 3, 1.5, 2),
(104, 19, 2, 3, 1.5, 2),
(105, 19, 1, 3, 1.5, 1),
(106, 19, 2, 3, 1.5, 1),
(107, 28, 1, 18, 9, 1),
(108, 28, 2, 18, 9, 1),
(109, 15, 1, 22, 11, 1),
(110, 15, 2, 22, 11, 1),
(111, 29, 1, 8, 4, 1),
(112, 29, 2, 8, 4, 1),
(113, 30, 1, 4, 2, 1),
(114, 30, 2, 4, 2, 1),
(115, 31, 1, 4, 2, 1),
(116, 31, 2, 4, 2, 1),
(117, 32, 1, 4, 2, 1),
(118, 32, 2, 4, 2, 1),
(119, 33, 1, 8, 16, 2),
(120, 33, 2, 8, 16, 2),
(121, 33, 1, 8, 16, 2),
(122, 33, 2, 8, 16, 2),
(123, 25, 1, 2, 1, 1),
(124, 25, 2, 2, 1, 1),
(125, 34, 1, 4.5, 9, 2),
(126, 34, 2, 4.5, 9, 2),
(127, 33, 1, 16, 8, 1),
(128, 33, 2, 16, 8, 1),
(129, 34, 1, 9, 4.5, 2),
(130, 34, 2, 9, 4.5, 2),
(131, 34, 1, 9, 4.5, 2),
(132, 34, 2, 9, 4.5, 2),
(133, 35, 1, 9, 4.5, 2),
(134, 35, 2, 9, 4.5, 2),
(135, 34, 1, 9, 4.5, 1),
(136, 34, 2, 9, 4.5, 1),
(137, 35, 1, 9, 4.5, 1),
(138, 35, 2, 9, 4.5, 1),
(139, 36, 1, 12, 6, 2),
(140, 36, 2, 12, 6, 2),
(141, 36, 1, 12, 6, 2),
(142, 36, 2, 12, 6, 2),
(143, 36, 1, 12, 6, 1),
(144, 36, 2, 12, 6, 1),
(145, 37, 1, 10, 5, 1),
(146, 37, 2, 10, 5, 1),
(147, 38, 1, 11, 6.5, 1),
(148, 38, 2, 11, 6.5, 1),
(149, 39, 1, 11, 6.5, 1),
(150, 39, 2, 11, 6.5, 1),
(151, 40, 1, 30, 19.5, 1),
(152, 40, 2, 30, 19.5, 1),
(153, 41, 1, 6, 3, 2),
(154, 41, 2, 6, 3, 2),
(155, 41, 1, 6, 3, 2),
(156, 41, 2, 6, 3, 2),
(157, 41, 1, 6, 3, 2),
(158, 41, 2, 6, 3, 2),
(159, 41, 1, 6, 3, 2),
(160, 41, 2, 6, 3, 2),
(161, 41, 1, 6, 3, 1),
(162, 41, 2, 6, 3, 1),
(163, 42, 1, 9, 4.5, 2),
(164, 42, 2, 9, 4.5, 2),
(165, 42, 1, 9, 4.5, 2),
(166, 42, 2, 9, 4.5, 2),
(167, 43, 1, 9, 4.5, 2),
(168, 43, 2, 9, 4.5, 2),
(169, 44, 1, 9, 4.5, 2),
(170, 44, 2, 9, 4.5, 2),
(171, 43, 1, 9, 4.5, 1),
(172, 43, 2, 9, 4.5, 1),
(173, 44, 1, 9, 4.5, 1),
(174, 44, 2, 9, 4.5, 1),
(175, 42, 1, 9, 4.5, 1),
(176, 42, 2, 9, 4.5, 1),
(177, 45, 1, 8, 16, 2),
(178, 45, 2, 8, 16, 2),
(179, 45, 1, 16, 8, 2),
(180, 45, 2, 16, 8, 2),
(181, 46, 1, 7, 3.5, 1),
(182, 46, 2, 7, 3.5, 1),
(183, 47, 1, 2, 1, 2),
(184, 47, 2, 2, 1, 2),
(185, 48, 1, 2, 1, 2),
(186, 49, 1, 2, 1, 2),
(187, 50, 1, 4, 2, 1),
(188, 48, 1, 2, 1, 1),
(189, 45, 1, 16, 8, 1),
(190, 45, 2, 16, 8, 1),
(191, 49, 1, 2, 1, 1),
(192, 47, 1, 2, 1, 2),
(193, 47, 2, 2, 1, 2),
(194, 51, 1, 2, 1, 1),
(195, 47, 1, 2, 1, 1),
(196, 47, 2, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_price_insurances`
--

CREATE TABLE IF NOT EXISTS `labo_item_price_insurances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labo_item_group_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_item_price_insurance_patient_group_details`
--

CREATE TABLE IF NOT EXISTS `labo_item_price_insurance_patient_group_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_item_price_insurance_id` int(11) DEFAULT NULL,
  `patient_group_id` int(11) DEFAULT NULL,
  `unit_price` float DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_medicines`
--

CREATE TABLE IF NOT EXISTS `labo_medicines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `labo_medicines`
--

INSERT INTO `labo_medicines` (`id`, `name`, `created`, `created_by`, `is_active`) VALUES
(1, 'A', '2019-05-04', 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_requests`
--

CREATE TABLE IF NOT EXISTS `labo_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `labo_id` bigint(20) DEFAULT NULL,
  `labo_item_group_id` bigint(20) DEFAULT NULL,
  `request` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `result` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `labo_sites`
--

CREATE TABLE IF NOT EXISTS `labo_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `labo_sites`
--

INSERT INTO `labo_sites` (`id`, `name`, `created`, `created_by`, `is_active`) VALUES
(1, 'A', '2019-05-04 09:21:04', 11, 1),
(2, 'b', '2019-05-04 09:21:29', 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_sub_title_groups`
--

CREATE TABLE IF NOT EXISTS `labo_sub_title_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

--
-- Dumping data for table `labo_sub_title_groups`
--

INSERT INTO `labo_sub_title_groups` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'A', '2019-05-04 09:14:05', 11, '2019-05-04 09:17:20', 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_title_groups`
--

CREATE TABLE IF NOT EXISTS `labo_title_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labo_item_group_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=11 ;

--
-- Dumping data for table `labo_title_groups`
--

INSERT INTO `labo_title_groups` (`id`, `labo_item_group_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '2, 3, 1, 30, 31, 32', 'HEMATOLOGY', '2019-05-01 14:28:29', 10, '2019-06-27 13:42:15', 7, 1),
(2, '48, 46, 50, 4, 38, 39, 22, 23, 21, 5, 47, 6, 7, 8, 36, 49, 9, 24, 25, 51', 'BIOCHEMISTRY', '2019-05-01 15:33:45', 10, '2020-06-23 15:30:28', 10, 1),
(3, '35, 11, 12, 15, 34, 10, 13, 40', 'SEROLOGY', '2019-05-01 15:45:31', 10, '2020-01-07 12:24:54', 10, 1),
(4, '37, 18, 19, 41', 'URINE', '2019-05-01 16:58:13', 10, '2020-01-16 14:51:19', 10, 1),
(5, '20', 'IONOGRAM', '2019-05-07 09:38:42', 10, '2019-05-07 09:38:42', NULL, 1),
(6, '16, 29, 17, 14, 28, 27, 33', 'RAPID TEST', '2019-05-07 10:52:55', 10, '2019-11-06 10:53:09', 10, 1),
(7, '', 'HORMONOLOGY', '2020-05-08 10:59:57', 10, '2020-05-08 10:59:57', 10, 2),
(8, '42, 43, 44', 'HORMONOLOGY', '2020-05-08 14:23:46', 10, '2020-05-08 14:23:46', NULL, 1),
(9, '', 'TUMOR MARKER', '2020-06-23 14:45:29', 10, '2020-06-23 14:45:29', 10, 2),
(10, '45', 'TUMOR MARKER', '2020-06-23 14:55:51', 10, '2020-06-23 14:55:51', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `labo_title_items`
--

CREATE TABLE IF NOT EXISTS `labo_title_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=8 ;

--
-- Dumping data for table `labo_title_items`
--

INSERT INTO `labo_title_items` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Numeration Globalaire', '2019-05-01 13:21:38', 10, '2019-05-01 13:21:38', NULL, 1),
(2, 'WBC Differential Cell', '2019-05-01 13:22:11', 10, '2019-05-01 13:22:11', NULL, 1),
(3, 'LIPID PROFILE', '2019-05-07 09:49:09', 10, '2019-05-07 09:49:09', NULL, 1),
(4, 'Urine macroscopic examination', '2019-05-09 15:27:38', 10, '2019-05-09 15:27:38', NULL, 1),
(5, 'Urine Cytology', '2019-05-09 15:27:53', 10, '2019-05-11 19:25:09', 10, 1),
(6, 'Urine Dipstick', '2019-05-09 15:28:06', 10, '2019-05-09 15:28:06', NULL, 1),
(7, 'Test', '2023-02-24 09:00:58', 1, '2023-02-24 09:00:58', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `labo_units`
--

CREATE TABLE IF NOT EXISTS `labo_units` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=57 ;

--
-- Dumping data for table `labo_units`
--

INSERT INTO `labo_units` (`id`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Testing', '', '2015-09-17 19:26:59', 1, '2015-09-17 19:26:59', 1, 2),
(2, 'Marady', NULL, '2015-09-21 20:17:06', 1, '2015-09-21 20:17:14', 1, 2),
(3, 'mg/dL', NULL, '2015-12-04 11:24:09', 1, '2015-12-04 11:24:31', 1, 1),
(4, 'mmol/dL', NULL, '2015-12-04 11:24:22', 1, '2015-12-04 11:24:22', NULL, 1),
(5, 'U/L', '', '2015-12-04 11:48:53', 1, '2015-12-04 11:48:53', 34, 2),
(6, 'g/dl', NULL, '2015-12-04 16:05:14', 1, '2015-12-04 16:05:14', NULL, 1),
(7, '%', NULL, '2015-12-04 16:05:36', 1, '2015-12-04 16:05:36', NULL, 1),
(8, 'mmol/l', NULL, '2015-12-04 16:06:49', 1, '2015-12-04 16:06:49', NULL, 1),
(9, 'IU/L', NULL, '2015-12-04 16:08:16', 1, '2015-12-04 16:08:16', NULL, 1),
(10, 'x10³/mm³', NULL, '2015-12-30 13:47:09', 34, '2015-12-30 13:47:09', NULL, 1),
(11, 'X10⁶/mm³', NULL, '2015-12-30 14:04:39', 34, '2015-12-30 14:04:39', NULL, 1),
(12, 'µm³', NULL, '2015-12-30 14:12:34', 34, '2015-12-30 14:12:34', NULL, 1),
(13, 'pg', NULL, '2015-12-30 14:13:21', 34, '2015-12-30 14:13:21', NULL, 1),
(14, 'INR', NULL, '2016-03-08 14:32:05', 1, '2016-03-08 14:32:05', NULL, 1),
(15, 'Second', NULL, '2016-03-08 14:34:56', 1, '2016-03-08 14:34:56', 8, 2),
(16, 'mm/h', NULL, '2016-03-10 12:50:13', 34, '2016-03-10 12:50:13', NULL, 1),
(17, 'µmol/L', '', '2016-03-15 14:05:25', 34, '2016-03-15 14:05:25', NULL, 1),
(18, 'µg/dL', '', '2016-03-15 14:23:11', 34, '2016-03-15 14:23:11', NULL, 1),
(19, 'U/L', '', '2016-03-15 14:31:58', 34, '2016-03-15 14:31:58', NULL, 1),
(20, 'mg/L', '', '2016-03-15 14:34:26', 34, '2016-03-15 14:34:26', NULL, 1),
(21, 'g/l', '', '2016-03-15 14:37:59', 34, '2016-03-15 14:37:59', NULL, 1),
(22, 'U/l', '', '2016-03-15 14:50:02', 34, '2016-03-15 14:50:02', NULL, 1),
(23, 'IU/ml', '', '2016-04-02 12:15:14', 34, '2016-04-02 12:15:14', NULL, 1),
(24, 'ng/ml', '', '2016-04-02 12:39:57', 34, '2016-04-02 12:39:57', NULL, 1),
(25, 'pg/ml', '', '2016-04-02 12:43:13', 34, '2016-04-02 12:43:13', NULL, 1),
(26, 'U/mL', '', '2016-04-02 13:00:14', 34, '2018-09-28 15:21:28', 8, 1),
(27, 'ng/dl', '', '2016-04-02 13:26:53', 34, '2016-04-02 13:26:53', NULL, 1),
(28, 'mIU/ml', '', '2016-04-02 13:28:03', 34, '2016-04-02 13:28:03', NULL, 1),
(29, 'cells/µl', '', '2016-06-23 12:52:05', 34, '2016-06-23 12:52:05', NULL, 1),
(30, 'cell/hpf', '', '2016-07-11 10:56:10', 34, '2016-07-11 10:56:10', NULL, 1),
(31, 'mg/mmol', '', '2016-07-14 11:58:45', 34, '2016-07-14 11:58:45', NULL, 1),
(32, 'mg/mmol', 'mg/mmol', '2016-07-14 12:06:31', 34, '2016-07-14 12:06:31', NULL, 1),
(33, 'copies/ml', '', '2016-09-29 12:35:03', 34, '2016-09-29 12:35:03', NULL, 1),
(34, 'x10³/µL', NULL, '2018-09-14 11:36:04', 8, '2018-09-14 11:36:04', NULL, 1),
(35, 'X10⁶/µL', NULL, '2018-09-14 11:38:26', 8, '2018-09-14 11:38:26', NULL, 1),
(36, 'fL', NULL, '2018-09-14 11:39:05', 8, '2018-09-14 11:39:05', NULL, 1),
(37, 'mn', NULL, '2018-09-14 15:27:48', 8, '2018-09-14 15:27:48', NULL, 1),
(38, 'Index', NULL, '2018-09-28 15:20:35', 8, '2018-09-28 15:20:35', NULL, 1),
(39, 'UI/L', NULL, '2018-09-28 15:21:11', 8, '2018-09-28 15:21:11', NULL, 1),
(40, 'mn/h', NULL, '2018-10-03 11:13:36', 8, '2018-10-03 11:13:36', NULL, 1),
(41, 'sec', NULL, '2018-10-03 13:27:34', 8, '2018-10-03 13:27:34', NULL, 1),
(42, 'nmol/L', NULL, '2018-10-04 12:22:29', 8, '2018-10-04 12:22:29', NULL, 1),
(43, 'UI/mL', NULL, '2018-10-04 13:52:07', 8, '2018-10-04 13:52:07', NULL, 1),
(44, 'Pg/dL', NULL, '2018-10-04 14:11:39', 8, '2018-10-04 14:11:39', NULL, 1),
(45, 'µUl/mL', NULL, '2018-10-04 14:13:22', 8, '2018-10-04 14:13:22', NULL, 1),
(46, 'Leu/µL', NULL, '2018-10-04 16:19:21', 8, '2018-10-04 16:19:21', NULL, 1),
(47, 'Ery/µL', NULL, '2018-10-04 16:19:38', 8, '2018-10-04 16:19:38', NULL, 1),
(48, 'cells', NULL, '2018-10-04 16:48:14', 8, '2018-10-04 16:48:14', NULL, 1),
(49, 'x10⁹/L', NULL, '2019-03-11 10:53:46', 10, '2019-03-11 10:56:57', 10, 1),
(50, '10⁹/L', '', '2019-03-11 10:54:45', 10, '2019-03-11 10:54:45', NULL, 1),
(51, '10^12/L', NULL, '2019-05-01 13:34:14', 10, '2019-05-01 13:34:14', NULL, 1),
(52, 'A', NULL, '2019-05-04 09:21:39', 11, '2019-05-04 09:21:39', NULL, 1),
(53, '/hpf', NULL, '2019-05-09 12:21:59', 10, '2019-05-09 12:21:59', NULL, 1),
(54, 'mL/min/1.73m2', NULL, '2020-06-23 12:30:29', 10, '2020-06-23 12:30:29', NULL, 1),
(55, 'mL/min/1.73m2', '', '2020-06-23 12:31:01', 10, '2020-06-23 12:31:01', NULL, 1),
(56, 'mL/min/1.73m2', 'mL/min/1.73m²', '2020-06-23 12:31:25', 10, '2020-06-23 12:31:25', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `landed_cost_types`
--

CREATE TABLE IF NOT EXISTS `landed_cost_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `landed_cost_types`
--
DROP TRIGGER IF EXISTS `zLandCostTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zLandCostTypeBfInsert` BEFORE INSERT ON `landed_cost_types`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `landing_costs`
--

CREATE TABLE IF NOT EXISTS `landing_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `landed_cost_type_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,9) DEFAULT NULL,
  `balance` decimal(15,9) DEFAULT NULL,
  `ap_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '-1: Edied; 0: Void; 1: Open; 2: Closed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `landing_cost_details`
--

CREATE TABLE IF NOT EXISTS `landing_cost_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landing_cost_id` int(11) DEFAULT NULL,
  `purchase_order_detail_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(15,1) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `small_val_uom` int(11) DEFAULT NULL,
  `unit_cost` decimal(18,9) DEFAULT NULL,
  `landed_cost` decimal(18,9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filter` (`landing_cost_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `landing_cost_receipts`
--

CREATE TABLE IF NOT EXISTS `landing_cost_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landing_cost_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_us` decimal(15,9) DEFAULT '0.000000000',
  `amount_other` decimal(15,9) DEFAULT '0.000000000',
  `total_amount` decimal(15,9) DEFAULT '0.000000000',
  `balance` decimal(15,9) DEFAULT '0.000000000',
  `balance_other` decimal(15,3) DEFAULT '0.000',
  `pay_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_void` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`landing_cost_id`),
  KEY `pv_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aisle` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bay` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_for_sale` tinyint(4) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `location_group_id` (`location_group_id`),
  KEY `key_search` (`is_for_sale`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `sys_code`, `location_group_id`, `name`, `color`, `level`, `aisle`, `bay`, `bin`, `position`, `created`, `created_by`, `modified`, `modified_by`, `is_for_sale`, `is_active`) VALUES
(1, 'd6d08e5324b88fb71579c3bbcfa5cb17', 1, 'Sale', '', '', '', '', '', '', '2017-08-30 13:35:30', 1, '2019-05-03 16:08:46', 1, 1, 1),
(2, 'f73b756364d7d7f41d7864c9cd338e63', 2, 'L1', '', '', '', '', '', 'L', '2019-05-03 16:09:08', 1, '2019-05-06 09:34:48', 7, 1, 2);

--
-- Triggers `locations`
--
DROP TRIGGER IF EXISTS `zLocationAfInsert`;
DELIMITER //
CREATE TRIGGER `zLocationAfInsert` AFTER INSERT ON `locations`
 FOR EACH ROW BEGIN
	INSERT INTO user_locations (user_id, location_id) SELECT id, NEW.id FROM users;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationBfInsert`;
DELIMITER //
CREATE TRIGGER `zLocationBfInsert` BEFORE INSERT ON `locations`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code IS NULL OR NEW.location_group_id IS NULL OR NEW.name = "" OR NEW.name IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationBfUpdate`;
DELIMITER //
CREATE TRIGGER `zLocationBfUpdate` BEFORE UPDATE ON `locations`
 FOR EACH ROW BEGIN
	DECLARE isCheck TINYINT(4);
	SELECT SUM(IFNULL(qty, 0)) INTO isCheck FROM inventories WHERE location_id = OLD.id;
	IF OLD.is_active = 1 AND NEW.is_active =2 THEN
		IF isCheck > 0 THEN 
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Data cloud not been delete';
		END IF;
   ELSE
   	IF OLD.location_group_id != NEW.location_group_id THEN
   		IF isCheck > 0 THEN
   			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot change warehouse';
   		END IF;
      ELSE 
      	IF NEW.location_group_id IS NULL OR NEW.name = "" OR NEW.name IS NULL THEN
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
			END IF;
   	END IF;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `location_groups`
--

CREATE TABLE IF NOT EXISTS `location_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_group_type_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `customer_id` int(11) DEFAULT NULL,
  `allow_negative_stock` tinyint(4) DEFAULT '0',
  `stock_tranfer_confirm` tinyint(4) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `warehouse_type_id` (`location_group_type_id`),
  KEY `searchs` (`name`,`is_active`,`code`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `location_groups`
--

INSERT INTO `location_groups` (`id`, `sys_code`, `location_group_type_id`, `code`, `name`, `description`, `customer_id`, `allow_negative_stock`, `stock_tranfer_confirm`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'f3e3fdbfe232d995fd16c8996aa6525e', 2, 'S001', 'Warehoues Stock', '', NULL, 1, 0, '2017-08-30 13:35:13', 1, '2019-02-11 10:45:54', 1, 1),
(2, 'e53b8a59c74403dbfd5f9339d5f36576', 3, 'l1', 'Wh-L1', '', NULL, 1, 0, '2019-05-03 16:08:32', 1, '2019-05-06 09:34:58', 7, 1);

--
-- Triggers `location_groups`
--
DROP TRIGGER IF EXISTS `zLocationGroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zLocationGroupBfInsert` BEFORE INSERT ON `location_groups`
 FOR EACH ROW BEGIN
   DECLARE isAllowNeg TINYINT(4);
   DECLARE isComTrans TINYINT(4);
	IF NEW.sys_code = "" OR NEW.sys_code IS NULL OR NEW.location_group_type_id IS NULL OR NEW.code = "" OR NEW.code IS NULL OR NEW.name = "" OR NEW.name IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
   ELSE 
   	SELECT allow_negative_stock, stock_tranfer_confirm  INTO isAllowNeg, isComTrans FROM location_group_types WHERE id = NEW.location_group_type_id;
	   SET NEW.allow_negative_stock = isAllowNeg;
	   SET NEW.stock_tranfer_confirm = isComTrans;
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationGroupBfUpdate`;
DELIMITER //
CREATE TRIGGER `zLocationGroupBfUpdate` BEFORE UPDATE ON `location_groups`
 FOR EACH ROW BEGIN
	DECLARE isCheck TINYINT(4);
	DECLARE isAllowNeg TINYINT(4);
	DECLARE isComTrans TINYINT(4);
	IF OLD.is_active = 1 AND NEW.is_active =2 THEN
		SELECT COUNT(id) INTO isCheck FROM locations WHERE location_group_id = OLD.id AND is_active = 1;
		IF isCheck > 0 THEN 
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Data cloud not been delete';
		END IF;
   ELSE 
   	IF NEW.location_group_type_id IS NULL OR NEW.code = "" OR NEW.code IS NULL OR NEW.name = "" OR NEW.name IS NULL THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	   ELSE 
	   	SELECT allow_negative_stock, stock_tranfer_confirm  INTO isAllowNeg, isComTrans FROM location_group_types WHERE id = NEW.location_group_type_id;
	   	SET NEW.allow_negative_stock = isAllowNeg;
	   	SET NEW.stock_tranfer_confirm = isComTrans;
		END IF;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `location_group_classese`
--

CREATE TABLE IF NOT EXISTS `location_group_classese` (
  `company_id` int(11) NOT NULL,
  `location_group_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_id`,`location_group_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location_group_classese`
--

INSERT INTO `location_group_classese` (`company_id`, `location_group_id`, `class_id`) VALUES
(1, 1, 1),
(1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `location_group_types`
--

CREATE TABLE IF NOT EXISTS `location_group_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `allow_negative_stock` tinyint(4) DEFAULT '0',
  `stock_tranfer_confirm` tinyint(4) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `name` (`name`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `location_group_types`
--

INSERT INTO `location_group_types` (`id`, `sys_code`, `name`, `description`, `allow_negative_stock`, `stock_tranfer_confirm`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '1fdc275e4fd22798c821a3098d56c88c', 'Consignment', NULL, 0, 0, '2017-08-23 15:01:19', NULL, '2017-08-23 15:01:21', NULL, 1),
(2, 'f63ecf61a3c39fc41d5ac793784db387', 'Warehouse Group', '', 1, 0, '2017-08-26 09:19:37', 1, '2019-03-03 15:35:28', 1, 1),
(3, '4c871358d9345b5fdfd75d70a9816142', 'WH-l1', '', 1, 0, '2019-05-03 16:07:00', 1, '2019-05-03 16:08:04', 1, 1);

--
-- Triggers `location_group_types`
--
DROP TRIGGER IF EXISTS `zLocationGroupTypeAfInsert`;
DELIMITER //
CREATE TRIGGER `zLocationGroupTypeAfInsert` AFTER INSERT ON `location_group_types`
 FOR EACH ROW BEGIN
	UPDATE location_groups SET allow_negative_stock = NEW.allow_negative_stock, stock_tranfer_confirm = NEW.stock_tranfer_confirm WHERE location_group_type_id = NEW.id;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationGroupTypeAfUpdate`;
DELIMITER //
CREATE TRIGGER `zLocationGroupTypeAfUpdate` AFTER UPDATE ON `location_group_types`
 FOR EACH ROW BEGIN
	IF NEW.is_active = 1 THEN 
		UPDATE location_groups SET allow_negative_stock = NEW.allow_negative_stock, stock_tranfer_confirm = NEW.stock_tranfer_confirm WHERE location_group_type_id = NEW.id;
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationGroupTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zLocationGroupTypeBfInsert` BEFORE INSERT ON `location_group_types`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zLocationGroupTypeBfUpdate`;
DELIMITER //
CREATE TRIGGER `zLocationGroupTypeBfUpdate` BEFORE UPDATE ON `location_group_types`
 FOR EACH ROW BEGIN
	DECLARE isCheck TINYINT(4);
	IF OLD.is_active = 1 AND NEW.is_active =2 THEN
		SELECT COUNT(id) INTO isCheck FROM location_groups WHERE location_group_type_id = OLD.id;
		IF isCheck > 0 THEN 
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Data cloud not been delete';
		END IF;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `location_settings`
--

CREATE TABLE IF NOT EXISTS `location_settings` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `modules` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `modules` (`modules`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `location_settings`
--

INSERT INTO `location_settings` (`id`, `modules`, `location_status`) VALUES
(1, 'PB', 0),
(2, 'BR', 0),
(3, 'POS', 0),
(4, 'Sales', 0),
(5, 'CM', 0);

--
-- Triggers `location_settings`
--
DROP TRIGGER IF EXISTS `zLocationSettingBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zLocationSettingBeforeDelete` BEFORE DELETE ON `location_settings`
 FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete default records';
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `medical_histories`
--

CREATE TABLE IF NOT EXISTS `medical_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_accouchements`
--

CREATE TABLE IF NOT EXISTS `mid_wife_accouchements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) DEFAULT NULL,
  `acconchem_rerme` int(11) DEFAULT '0',
  `accon_chement` int(11) DEFAULT '0',
  `anormat` int(11) DEFAULT '0',
  `acc_par_ventonse` int(11) DEFAULT '0',
  `caesarean` int(11) DEFAULT '0',
  `g` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time1` time DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `girl` int(11) DEFAULT '0',
  `boy` int(11) DEFAULT '0',
  `weight` float DEFAULT NULL,
  `long` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `head_size` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `one_minute` int(11) DEFAULT NULL,
  `five_minute` int(11) DEFAULT NULL,
  `ten_minute` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT '0',
  `not_good` int(11) DEFAULT '0',
  `little` int(11) DEFAULT '0',
  `much` int(11) DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`),
  KEY `mid_wife_dossier_medical_id` (`mid_wife_dossier_medical_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_accouchement_first_times`
--

CREATE TABLE IF NOT EXISTS `mid_wife_accouchement_first_times` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) NOT NULL,
  `time` time DEFAULT NULL,
  `first_blood` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_ta` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_p` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_temperature` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_accouchement_next_times`
--

CREATE TABLE IF NOT EXISTS `mid_wife_accouchement_next_times` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) NOT NULL,
  `next_time` time DEFAULT NULL,
  `next_blood` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_ta` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_p` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_temperature` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_allaitements`
--

CREATE TABLE IF NOT EXISTS `mid_wife_allaitements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) NOT NULL,
  `soon` int(4) NOT NULL DEFAULT '0',
  `two_houre_after` int(4) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:active 3:edit 2:old data',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_births`
--

CREATE TABLE IF NOT EXISTS `mid_wife_births` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `time` time NOT NULL,
  `mid_wife_dossier_medical_id` int(11) NOT NULL,
  `bcf` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pdf` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ta` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pouls` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_birth_details`
--

CREATE TABLE IF NOT EXISTS `mid_wife_birth_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) NOT NULL,
  `hydramnios` int(4) NOT NULL DEFAULT '0',
  `excess` int(4) NOT NULL DEFAULT '0',
  `normal` int(4) NOT NULL DEFAULT '0',
  `oligoamnios` int(4) NOT NULL DEFAULT '0',
  `whitish` int(4) NOT NULL DEFAULT '0',
  `clear` int(4) NOT NULL DEFAULT '0',
  `greenish` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_active` int(4) NOT NULL DEFAULT '1' COMMENT '1:active 2:old data 3:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_check_up_patients`
--

CREATE TABLE IF NOT EXISTS `mid_wife_check_up_patients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_service_id` int(11) NOT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `blood_pressure` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pulse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `presentation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uterus_height` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `baby_heart_rate` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iron` int(11) DEFAULT NULL,
  `edema` int(11) DEFAULT '0',
  `albuminuria` int(11) DEFAULT '0',
  `asthma` int(11) DEFAULT '0',
  `other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_appointment` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_deliverances`
--

CREATE TABLE IF NOT EXISTS `mid_wife_deliverances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_dossier_medical_id` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `weight` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `beaudelauque` int(11) DEFAULT NULL,
  `duncan` int(11) DEFAULT NULL,
  `check` int(11) DEFAULT NULL,
  `natural` int(11) DEFAULT NULL,
  `by_hand` int(11) DEFAULT NULL,
  `have` int(11) DEFAULT NULL,
  `no_have` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_dossier_medicals`
--

CREATE TABLE IF NOT EXISTS `mid_wife_dossier_medicals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `patient_relative_id` int(11) NOT NULL,
  `entre_le` date DEFAULT NULL,
  `doagnostic_entre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortie_le` date DEFAULT NULL,
  `doagnostic_sortie` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acconchement_rerme` int(11) DEFAULT NULL,
  `accon_chement` int(11) DEFAULT NULL,
  `avortment_inv` int(11) DEFAULT NULL,
  `baby` int(11) DEFAULT NULL,
  `caesarean` int(11) DEFAULT NULL,
  `hemorrhage` int(11) DEFAULT NULL,
  `hypertension` int(11) DEFAULT NULL,
  `heart` int(11) DEFAULT NULL,
  `other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ta` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `p` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `t` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `presentation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hu` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bcf` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pde` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `edema` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_services`
--

CREATE TABLE IF NOT EXISTS `mid_wife_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid_wife_service_queue_id` int(11) DEFAULT NULL,
  `last_mentstruation_period` date DEFAULT NULL,
  `estimate_delivery_date` date DEFAULT NULL,
  `echo` date DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `gestation` int(11) DEFAULT NULL,
  `baby` int(11) DEFAULT NULL,
  `abortion` int(11) DEFAULT '0',
  `interuption_volontain` int(11) DEFAULT '0',
  `birth` int(11) DEFAULT NULL,
  `nee_moit` int(11) DEFAULT NULL,
  `mort_nee` int(11) DEFAULT NULL,
  `acconchement_normal` int(11) DEFAULT NULL,
  `caesarean` int(11) DEFAULT NULL,
  `acc_par_ventonse` int(11) DEFAULT NULL,
  `edema` int(11) DEFAULT '0',
  `albuminuria` int(11) DEFAULT '0',
  `cadiojathie` int(11) DEFAULT '0',
  `asthma` int(11) DEFAULT '0',
  `other` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1' COMMENT '1:active 2:edit',
  PRIMARY KEY (`id`),
  KEY `mid_wife_service_queue_id` (`mid_wife_service_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_service_requests`
--

CREATE TABLE IF NOT EXISTS `mid_wife_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `mid_wife_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:mid not do 2:mid do ready',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mid_wife_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `mid_wife_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `mid_wife_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:mid not do 2:mid do ready',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_type_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0: Disabled; 1: Enable',
  `type` tinyint(4) DEFAULT NULL COMMENT '1: Normal; 2: Admin; 3: Specail Price',
  `price` decimal(15,3) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `module_type_id` (`module_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=863 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `sys_code`, `module_type_id`, `name`, `ordering`, `status`, `type`, `price`, `description`) VALUES
(1, 'ecbf7b0b55c01774415c6028002a801d', 1, 'Home Page', 1, 1, NULL, NULL, NULL),
(2, 'ce4ec441a886f7b17ae6c2c765b7d537', 2, 'User (view)', 1, 1, NULL, NULL, NULL),
(6, 'dab1e688c169a92c40db4ba93564c601', 3, 'Group (view)', 1, 1, NULL, NULL, NULL),
(10, 'e15c044b89daf222176327ed34c54c3a', 4, 'Location (view)', 1, 1, NULL, NULL, NULL),
(11, 'aabc17e90898fb8f51dbd85ea36ff710', 4, 'Location (add)', 2, 1, NULL, NULL, NULL),
(12, '04c82aeb5c4c4f0d5b1fc9f99ebf2ca0', 4, 'Location (edit)', 3, 1, NULL, NULL, NULL),
(13, '440588775bc55dd0a8964c3236360c1b', 4, 'Location (delete)', 4, 1, NULL, NULL, NULL),
(14, '8f9f63af900f219336e944c32c110b64', 5, 'Warehouse (view)', 1, 1, NULL, NULL, NULL),
(15, '63e0f44cfd878d5f4fd28dd805c4d3d6', 5, 'Warehouse (add)', 2, 1, NULL, NULL, NULL),
(16, '107b8799c7d83b769b0ee033cf198b92', 5, 'Warehouse (edit)', 3, 1, NULL, NULL, NULL),
(17, '0f99f0d432c9cc77164f3f669eb8cecb', 5, 'Warehouse (delete)', 4, 1, NULL, NULL, NULL),
(18, 'ecd71e9342c04ee6a03bd6b63f87071d', 6, 'UoM (view)', 1, 1, NULL, NULL, NULL),
(19, '76a347b9789508940529bbdeee6caf19', 6, 'UoM (add)', 2, 1, NULL, NULL, NULL),
(20, '8101df8eb736d1831a5674b3ca85d10c', 6, 'UoM (edit)', 3, 1, NULL, NULL, NULL),
(21, 'a1ae4eae873519b3d05cd413d69f8ebc', 6, 'UoM (delete)', 4, 1, NULL, NULL, NULL),
(22, 'c37cf5f94295461732d16af73a0e49b7', 7, 'UoM Conversion (view)', 1, 1, NULL, NULL, NULL),
(23, '41b2e3fb781af5c6458b6ba6e8c5e71d', 7, 'UoM Conversion (add)', 2, 1, NULL, NULL, NULL),
(24, '1e3bf330c03b26012c7ccba688055933', 7, 'UoM Conversion (edit)', 3, 1, NULL, NULL, NULL),
(25, '0b6ac15e77d0f007e0974e52608314dd', 7, 'UoM Conversion (delete)', 4, 1, NULL, NULL, NULL),
(26, 'addda134ecb17a631d792a7b20933ee5', 8, 'Province (view)', 1, 1, NULL, NULL, NULL),
(27, '2508852fae1a09ef50273ab30ca0ca3d', 8, 'Province (add)', 2, 1, NULL, NULL, NULL),
(28, 'df3d2c9a62cc745bb8e79b48be629a17', 8, 'Province (edit)', 3, 1, NULL, NULL, NULL),
(29, '220d2bc01df3bfebbb7ea08866b30cc4', 8, 'Province (delete)', 4, 1, NULL, NULL, NULL),
(30, '731ceb379f88c2c2a9cdd5843b3a0066', 9, 'District (view)', 1, 1, NULL, NULL, NULL),
(31, '7f7c19132653126af3110c548a291543', 9, 'District (add)', 2, 1, NULL, NULL, NULL),
(32, '16d243a598bbfabec9f4005783cf6288', 9, 'District (edit)', 3, 1, NULL, NULL, NULL),
(33, '2df927297a76e5117432a84f00f2ec20', 9, 'District (delete)', 4, 1, NULL, NULL, NULL),
(34, 'b2fcac831c999b046c0a2d301e3a3c22', 10, 'Commune (view)', 1, 1, NULL, NULL, NULL),
(35, 'b6fab3124af26e75a1e89804568a8278', 10, 'Commune (add)', 2, 1, NULL, NULL, NULL),
(36, '193d7f58046be13185076ff0e117b8a4', 10, 'Commune (edit)', 3, 1, NULL, NULL, NULL),
(37, '957f0573547bd095757aa6409a999340', 10, 'Commune (delete)', 4, 1, NULL, NULL, NULL),
(38, 'fd884f2e605d7653fe4ad2b7704321c2', 11, 'Village (View)', 1, 1, NULL, NULL, NULL),
(39, '379149e9041e2a493f19aa6f06dcbd2b', 11, 'Village (add)', 2, 1, NULL, NULL, NULL),
(40, '2d310d8996cb40b51e84cbc8bcc79f2b', 11, 'Village (edit)', 3, 1, NULL, NULL, NULL),
(41, '74a3f873e163d2f144c9d60099932963', 11, 'Village (delete)', 4, 1, NULL, NULL, NULL),
(42, '7d63b836bc78c86f492f472a3f240d3b', 12, 'Customer Group (view)', 1, 1, NULL, NULL, NULL),
(43, '42b84ba6edb8c5a550449828ff4e221e', 12, 'Customer Group (add)', 2, 1, NULL, NULL, NULL),
(44, 'eb9f99d07775882c604a70f0acffad6b', 12, 'Customer Group (edit)', 3, 1, NULL, NULL, NULL),
(45, '5f80a642f16c136805955f564f1bbdb0', 12, 'Customer Group (delete)', 4, 1, NULL, NULL, NULL),
(46, '3c0c9d317b3ecbe53260fe0a20183e0e', 13, 'Product (View)', 1, 1, 3, '15.000', '(Admin Modules)'),
(47, '1e2918aea1d0b9a23c117f376819117a', 13, 'Product (Add)', 2, 1, NULL, NULL, NULL),
(48, '2457b8222bf59bb390fa31a32be57b12', 13, 'Product (Edit)', 3, 1, NULL, NULL, NULL),
(49, 'b6b18a2c32b510bc970b73c7e36d93b8', 13, 'Product (Delete)', 4, 1, NULL, NULL, NULL),
(50, '1a6e4c9badee9698174270a7cf51ab5c', 14, 'Customer (view)', 1, 1, NULL, NULL, NULL),
(51, 'ee58edde39ddd8e5fc8e075d0103df86', 14, 'Customer (add)', 2, 1, NULL, NULL, NULL),
(52, '3c388ada21af2fea79bde4b405adbee0', 14, 'Customer (edit)', 3, 1, NULL, NULL, NULL),
(53, 'f77c473f61fcaf30343e4a114b9ae6ff', 14, 'Customer (delete)', 4, 1, NULL, NULL, NULL),
(55, '48f0c5ea04b22a34e61d30cce9434aeb', 15, 'Physical Count (Add)', 2, 1, NULL, NULL, NULL),
(60, '69a7f9bcc58dac8ff84a7c7b6508d0f5', 17, 'Product Group (View)', 1, 1, NULL, NULL, NULL),
(61, '8bbe22acec22f3b9ff266ee6fc916e51', 17, 'Product Group (Add)', 2, 1, NULL, NULL, NULL),
(62, '20b2f140aa9a88b3dd4ed964f6002b5d', 17, 'Product Group (Edit)', 3, 1, NULL, NULL, NULL),
(63, '95a7085d9922610a8b6fa5d7ddd1d404', 17, 'Product Group (Delete)', 4, 1, NULL, NULL, NULL),
(72, 'b6b3023acd6a3473e08f12f9e2afdfc8', 22, 'Vendor (View)', 1, 1, NULL, NULL, NULL),
(73, 'fe6ca5259f6947f18178ccbc8e512d7d', 22, 'Vendor (Add)', 2, 1, NULL, NULL, NULL),
(74, 'a0ce84b21019478a0452454797213b5e', 22, 'Vendor (Edit)', 3, 1, NULL, NULL, NULL),
(75, '83bdf0030e0065dc11c0abb21a33ad17', 22, 'Vendor (Delete)', 4, 1, NULL, NULL, NULL),
(76, 'b66765d1a6f093d4a7b98edbc149912d', 18, 'Expense Type (view)', 1, 1, NULL, NULL, NULL),
(77, '5736840571a5024f15a608263d32ff53', 18, 'Expense Type (add)', 2, 1, NULL, NULL, NULL),
(78, '3523eaccb6acf76096e137ffed58ca15', 18, 'Expense Type (edit)', 3, 1, NULL, NULL, NULL),
(79, 'a1663f6de212298017205a21d8d70456', 18, 'Expense Type (change status)', 5, 1, NULL, NULL, NULL),
(80, '8f594271f69e74579383530fa810a509', 19, 'Chart of Account Type (view)', 1, 1, NULL, NULL, NULL),
(83, '6c0eaf03a4a6fd9633438553a01ac1db', 19, 'Chart of Account Type (change status)', 2, 1, NULL, NULL, NULL),
(84, '9cd8402e9fd6509d3f8d4b0e144e7396', 20, 'Chart of Account Group (view)', 1, 1, NULL, NULL, NULL),
(85, '4bf787bab765359b9659e5c0c7e02eb1', 20, 'Chart of Account Group (add)', 2, 1, NULL, NULL, NULL),
(86, 'fd5a09a7925804ae5997b2df6401cb79', 20, 'Chart of Account Group (edit)', 3, 1, NULL, NULL, NULL),
(87, '229db8dea5a05e9efa037075047fdcec', 20, 'Chart of Account Group (change status)', 5, 1, NULL, NULL, NULL),
(88, '43d0662732b8e01545563b16c6c68bdf', 15, 'Physical Count (Edit)', 3, 1, NULL, NULL, NULL),
(91, '5296caaa616322b638420bfc807e62f4', 21, 'Purchase Bill (View)', 1, 1, NULL, NULL, NULL),
(92, '9ec61e8e4995f41555bbdfca72439164', 21, 'Purchase Bill (Add)', 2, 1, NULL, NULL, NULL),
(94, '0c977394917646b90c066e64d8f2d898', 21, 'Purchase Bill (Void)', 5, 1, NULL, NULL, NULL),
(96, 'c70d80c20ee510dc94f922200fe207f2', 23, 'Purchase Receive (View)', 1, 1, NULL, NULL, NULL),
(97, '193608586cb34b77472b012fb7e333e9', 23, 'Purchase Receive (Receive)', 2, 1, NULL, NULL, NULL),
(101, '20518a2d24d7dfb52a48c232c6965512', 24, 'Transfer Order (View)', 1, 1, NULL, NULL, NULL),
(102, 'e8bfc95f7fc97a668ffa29db62ab8aa9', 24, 'Transfer Order (Add)', 2, 1, NULL, NULL, NULL),
(103, 'd6796071e4f0e1f453ca6a663cebe094', 24, 'Transfer Order (Edit)', 3, 1, NULL, NULL, NULL),
(104, '8e57b54ac4e9205f8a79a8ae21e99506', 24, 'Transfer Order (Delete)', 4, 1, NULL, NULL, NULL),
(105, '156a620354a863006c179fed47d08da3', 25, 'Sales / Invoice (View)', 1, 1, NULL, NULL, NULL),
(106, 'fdac331385443ebeb83c167d966bd863', 25, 'Sales / Invoice (Add)', 2, 1, NULL, NULL, NULL),
(107, '2aae5a44c0e4fd1dfd211db060d9e92a', 25, 'Sales / Invoice (Aging)', 4, 1, NULL, NULL, NULL),
(108, '479c0bddfa886559b61b4140df433289', 25, 'Sales / Invoice (Void)', 5, 1, NULL, NULL, NULL),
(109, 'e5d00f82f48ee6ce10f047f8e1ed1117', 26, 'Exchange Rate (view)', 1, 1, NULL, NULL, NULL),
(110, '0ef778d9144c34d3d2b1e709a166093b', 26, 'Exchange Rate (add)', 2, 1, NULL, NULL, NULL),
(113, '4f0fdf2f9fcef7ecb8d9b1e37e812884', 27, 'Journal Entry (view)', 1, 1, NULL, NULL, NULL),
(114, 'd694b5b5ed78d0c8f3c114c5f52cc49b', 27, 'Journal Entry (add)', 2, 1, NULL, NULL, NULL),
(115, '97ee3c56688449de3c4dafcd4becc1b8', 27, 'Journal Entry (edit)', 5, 1, NULL, NULL, NULL),
(116, '33ee25672fa03a1608997937451abb4e', 27, 'Journal Entry (delete)', 6, 1, NULL, NULL, NULL),
(117, '9cc1644a859e63e4922bd1ffbb6397e8', 28, 'Report (GL)', 801, 1, NULL, NULL, NULL),
(118, '1915a7cf45ba634845f2bc9c83bb2b2e', 28, 'Report (TB)', 802, 0, NULL, NULL, NULL),
(119, 'f719c717c8b7b9fcf269e70134d97476', 28, 'Report (P&L)', 803, 1, NULL, NULL, NULL),
(120, '2180bc0df9cb682b2110968285c03297', 28, 'Report (BS)', 804, 0, NULL, NULL, NULL),
(121, 'a4d90f50cd762336a9864a6de6c7217b', 29, 'Discount (view)', 1, 1, NULL, NULL, NULL),
(122, 'd38c4d61c8650ceaf057b3bbdb50837f', 29, 'Discount (add)', 2, 1, NULL, NULL, NULL),
(123, '84dfbdc956ba9045c405b6b237f554b8', 29, 'Discount (edit)', 3, 1, NULL, NULL, NULL),
(124, '9edccda47b55d3e8eb09eea7e65207b2', 29, 'Discount (delete)', 4, 1, NULL, NULL, NULL),
(125, 'ba522d0088641d9d81b439be7f95a3d1', 30, 'Transfer Receive (View)', 1, 1, NULL, NULL, NULL),
(126, '53b0614ff35960d583272eb86a2b495d', 30, 'Transfer Receive (Receive)', 2, 1, NULL, NULL, NULL),
(128, 'efc9d48d015ba0c8f29c17f3c49b24e5', 31, 'Company (view)', 1, 1, NULL, NULL, NULL),
(129, '1e23e3ba2f866599be5a88e79d8bfdd4', 31, 'Company (add)', 2, 0, NULL, NULL, NULL),
(130, '33639bc6156410335a4b8c4f10d9f144', 31, 'Company (edit)', 3, 1, NULL, NULL, NULL),
(131, '37e6cdd363dc137c8979cfdcde8fd065', 31, 'Company (delete)', 4, 0, NULL, NULL, NULL),
(132, '6b53ee678a5dc48a8d1fbe5bba4e2d59', 28, 'Report (Customer Address List)', 404, 0, NULL, NULL, NULL),
(133, 'e92e77ca86d2b60ff313fbd8c81f9fda', 28, 'Report (Customer Address Detail)', 405, 0, NULL, NULL, NULL),
(134, '1378c58fbf55fd15bcbe501fe3e8e218', 28, 'Report (Product Expiry Date & Aging)', 105, 1, NULL, NULL, NULL),
(135, '5609598695a3cd3abf5d78f57166e964', 28, 'Report (CF)', 805, 0, NULL, NULL, NULL),
(136, '14cfd9aab27f07fa72239d435939eec3', 13, 'Product (Set Price)', 5, 1, NULL, NULL, NULL),
(137, '64a3181f5cc821ebd2e9494be0012927', 32, 'Section (view)', 1, 1, NULL, NULL, NULL),
(138, '2cef528b67b5abbec391a9f9260001fd', 32, 'Section (add)', 2, 1, NULL, NULL, NULL),
(139, '52ba3cddf1240abcbe68351ae5e588ef', 32, 'Section (edit)', 3, 1, NULL, NULL, NULL),
(140, '247ed791ca49c640dddee91a18bdf167', 32, 'Section (delete)', 4, 1, NULL, NULL, NULL),
(141, 'c6f52e11f727221458d3227e66cc2413', 33, 'Service (view)', 1, 1, NULL, NULL, NULL),
(142, 'cf92c3514eda243831d97d29f7e6364b', 33, 'Service (add)', 2, 1, NULL, NULL, NULL),
(143, '62818e087ba7fc6bda5cd5aa8b06ee85', 33, 'Service (edit)', 3, 1, NULL, NULL, NULL),
(144, '6f81b36e5218a6aef104ed6ed70e4cd8', 33, 'Service (delete)', 4, 1, NULL, NULL, NULL),
(145, '9c61ad108f6c38328506d3f4ee039691', 21, 'Purchase Bill (Aging)', 4, 1, NULL, NULL, NULL),
(147, '1be1c4f6bdbf34abaf1b02d22287c438', 25, 'Sales / Invoice (Add Misc.)', 7, 1, NULL, NULL, NULL),
(148, '5c679a2e4a0b7d980de1408fb537f0b1', 25, 'Sales / Invoice (Add Service)', 6, 1, NULL, NULL, NULL),
(149, '3bbaefb51653ea6bd26fc0ed1e33ca51', 34, 'Point Of Sales (Add)', 2, 1, NULL, NULL, NULL),
(151, '677d9a5dfa2c54d2b9230058d8777a26', 34, 'Point Of Sales (Print)', 3, 1, NULL, NULL, NULL),
(152, 'ac5962139cd478f36a41b941db603a27', 34, 'Point Of Sales (Void)', 4, 1, NULL, NULL, NULL),
(153, '44f70f55a8a8c6c96a0c6e0c8f6d3621', 28, 'Report (A/R)', 401, 1, NULL, NULL, NULL),
(154, 'b467541dd5d800ef6d7ee75ac6e4b3ab', 28, 'Report (A/P)', 701, 1, NULL, NULL, NULL),
(155, '7b9dfd34a3f19b1201c3f383cdf66920', 28, 'Report (Purchase Bill Barcode)', 601, 0, NULL, NULL, NULL),
(156, 'fd1b64ae75b31d4c6537635dab1f198f', 35, 'Budget Plan (P&L) (view)', 1, 1, NULL, NULL, NULL),
(157, '51507385fdc118d2cd46c35dd57d71d8', 35, 'Budget Plan (P&L) (add)', 2, 1, NULL, NULL, NULL),
(158, 'db2790aa9087d7146741e5227081d5e4', 35, 'Budget Plan (P&L) (edit)', 3, 1, NULL, NULL, NULL),
(159, '3b523e7b99db626a6f9f651dbf2b1053', 35, 'Budget Plan (P&L) (delete)', 4, 1, NULL, NULL, NULL),
(160, 'b380af810a6e9b98f96c67428a64d927', 36, 'Class (view)', 1, 1, NULL, NULL, NULL),
(161, '1ce03af0e0d2d79bcae4ba6a399b579e', 36, 'Class (add)', 2, 1, NULL, NULL, NULL),
(162, '7a5993398406f97e52f87cb2fa4f8787', 36, 'Class (edit)', 3, 1, NULL, NULL, NULL),
(163, '823cc8e936bf6903f1730a79815eaea0', 36, 'Class (delete)', 4, 1, NULL, NULL, NULL),
(164, '10f76f75cd8604c228249a0d934ea22e', 25, 'Sales / Invoice (Print Invoice)', 9, 1, NULL, NULL, NULL),
(166, '1a89db893c342f93317d2d88803e6db6', 28, 'Report (Product Average Cost)', 110, 1, NULL, NULL, NULL),
(167, '84ccfde6f13a7e89e46d39a7f0219e13', 28, 'Report (Product Price List)', 111, 1, NULL, NULL, NULL),
(168, 'd0c7dedb0617d843e18299d992512042', 28, 'Report (Dormant Customer)', 406, 0, NULL, NULL, NULL),
(169, '49f353e521287d39e079eb303b30aff1', 28, 'Report (Discount Summary)', 313, 1, NULL, NULL, NULL),
(170, 'e8e617c087e344932655c5c02bd68140', 28, 'Report (User Rights)', 901, 1, NULL, NULL, NULL),
(171, '5ea25e16439053b553af27d771f0a397', 28, 'Report (User Logs)', 902, 1, NULL, NULL, NULL),
(172, 'fd59b50a19e90d312224a2fac8de7f1b', 28, 'Report (Vendor Product List)', 703, 0, NULL, NULL, NULL),
(173, '194e80784f901f1494c2707798949842', 28, 'Report (Vendor Address)', 704, 1, NULL, NULL, NULL),
(174, '117f150b48c89a2a531960643aaf9c12', 28, 'Report (Vendor Address List)', 705, 0, NULL, NULL, NULL),
(176, '2367a36aefa9a29de24a61e1d6a8ba30', 37, 'ICS', 1, 1, NULL, NULL, NULL),
(177, '8583c0e36d87e6e4225f927e41d20448', 27, 'Journal Entry (write checks)', 3, 1, NULL, NULL, NULL),
(181, 'e3b4866b87ec0b0790018a795419947d', 25, 'Sales / Invoice (Add Discount)', 8, 1, NULL, NULL, NULL),
(182, '9e066cfd2cb14bcc69eff803e257dfd8', 34, 'Point Of Sales (Add Total Discount)', 7, 1, NULL, NULL, NULL),
(186, '43d55adbbe77a0ccc601e72609e4ca8e', 27, 'Journal Entry (supervisor level) (view)', 7, 1, NULL, NULL, NULL),
(187, '22cefbcf2257d7547d0f0ab2b027515c', 27, 'Journal Entry (supervisor level) (add)', 8, 1, NULL, NULL, NULL),
(188, '1f7533493799e8a6751f36e554d3f893', 27, 'Journal Entry (make deposits)', 4, 1, NULL, NULL, NULL),
(189, 'dc85c8c9b9f79e41a73a9222f9024f52', 27, 'Journal Entry (supervisor level) (edit)', 9, 1, NULL, NULL, NULL),
(190, '75f16e69d134c37b69cb25a4664655ee', 27, 'Journal Entry (supervisor level) (delete)', 10, 1, NULL, NULL, NULL),
(192, 'd0a48c03c7c1febeb18aacc086634888', 28, 'Report (Sales/Invoice)', 309, 1, NULL, NULL, NULL),
(193, '166b99a348eaa63b7af6d7afbd619669', 39, 'Payment Terms (view)', 1, 1, NULL, NULL, NULL),
(194, 'c222b82bda2f8af7593fca3af4ae6b85', 39, 'Payment Terms (add)', 2, 1, NULL, NULL, NULL),
(195, '2ea4a3a498d82373de5b8f1c1456acbb', 39, 'Payment Terms (edit)', 3, 1, NULL, NULL, NULL),
(196, 'afaa0c48601c30622f75e2ee79c1f5a0', 39, 'Payment Terms (delete)', 4, 1, NULL, NULL, NULL),
(197, '34b15da0ce5877a33190a92b91c06dde', 28, 'Report (Global Inventory)', 101, 1, NULL, NULL, NULL),
(198, '4310065f9994e9d19dd8b946b13954fc', 28, 'Report (Invoice Purchase Bill)', 603, 1, NULL, NULL, NULL),
(199, '8548626b74a0a4ab2caec3d2bb987f5b', 18, 'Expense Type (delete)', 4, 1, NULL, NULL, NULL),
(200, 'b025f9150bb193d737cc771d2c930255', 20, 'Chart of Account Group (delete)', 4, 1, NULL, NULL, NULL),
(201, '9a353be0b11333d8403c6271484e15e2', 21, 'Purchase Bill (Edit)', 3, 1, NULL, NULL, NULL),
(202, '72d4d60bf1af20e964e3414c317a2ad9', 40, 'Sales Return (View)', 1, 1, NULL, NULL, NULL),
(203, 'dd22e7eb28bdc5fcf19d26207c98020b', 40, 'Sales Return (Add)', 2, 1, NULL, NULL, NULL),
(204, 'c79ff5c33025c5f945b1d621ce848f3a', 40, 'Sales Return (Aging)', 4, 1, NULL, NULL, NULL),
(205, 'fe4495451e9340ebc8f51b2680044aa3', 41, 'Purchase Return (View)', 1, 1, NULL, NULL, NULL),
(206, '5698a1eb2cd1318a861988fb5db7316e', 41, 'Purchase Return (Add)', 2, 1, NULL, NULL, NULL),
(207, '9618a5e9e9ff583b2d44b024b1c072e6', 41, 'Purchase Return (Aging)', 4, 1, NULL, NULL, NULL),
(208, '99f9d2eb89be73bae4f58868d9ecf87d', 37, 'ICS', 2, 1, NULL, NULL, NULL),
(209, '57f685e16cb08d956752f97c5cb1bd96', 42, 'Receive Payments', 1, 1, NULL, NULL, NULL),
(210, '35f2bdef76fa5e33edd89d035ee374d9', 43, 'Pay Bills', 1, 1, NULL, NULL, NULL),
(211, 'ff81c5e3e549f6d84c3aace0f0a92e74', 25, 'Sales / Invoice (Edit)', 3, 1, NULL, NULL, NULL),
(212, '82e7cc08d11f0b0c9e2bf1ac35403738', 28, 'Report (Invoice Credit Memo)', 311, 1, NULL, NULL, NULL),
(213, '248241649c339ebc0847f338ea2786bc', 28, 'Report (Invoice Purchase Bill Credit)', 604, 1, NULL, NULL, NULL),
(214, '8a87e1b04807d431874194515c8053b4', 28, 'Report (Customer Balance)', 402, 1, NULL, NULL, NULL),
(215, '83ca888621272dc4a2890fe3778ffcd3', 28, 'Report (Vendor Balance)', 702, 1, NULL, NULL, NULL),
(216, '19ee660b21dba50f9832bce6cd5d6e83', 40, 'Sales Return (Add Service)', 6, 1, NULL, NULL, NULL),
(217, '5d626195b188a5ef49e06432e1bd0b13', 40, 'Sales Return (Add Misc.)', 7, 1, NULL, NULL, NULL),
(218, 'd98aa9b147555f1de57e1a802e2dd002', 41, 'Purchase Return (Add Service)', 6, 1, NULL, NULL, NULL),
(219, 'e1107944766c55b579bf54b596f3dfa9', 41, 'Purchase Return (Add Misc.)', 7, 1, NULL, NULL, NULL),
(220, '0945d506ae7cfb438251e9dc307bc886', 40, 'Sales Return (Void)', 5, 1, NULL, NULL, NULL),
(221, '00f9d27507806c53056f0ab04305b3a7', 40, 'Sales Return (Edit)', 3, 1, NULL, NULL, NULL),
(222, '3f7df921abb88cc2bd10b6d8ea7243a0', 41, 'Purchase Return (Edit)', 3, 1, NULL, NULL, NULL),
(223, '9094c7b038fa12723ae7804157a5d710', 41, 'Purchase Return (Void)', 5, 1, NULL, NULL, NULL),
(224, '773b8bf38a22a57ac3d352acca693f6b', 44, 'Receive Payments (Journal)', 1, 1, NULL, NULL, NULL),
(225, '4c1067ae2e663707dc8102404016da87', 45, 'Pay Bills (Journal)', 1, 1, NULL, NULL, NULL),
(226, '5331f8f20030930a753534e3179a76ca', 34, 'Point Of Sales (Add service)', 5, 1, NULL, NULL, NULL),
(228, '0fd5b9ad7255795946bbf93fc577c84e', 28, 'Report (Sales By Item Type POS)', 302, 0, NULL, NULL, NULL),
(229, 'a56bd6cdb72ed8f807ca4fa6675b3af8', 28, 'Report (Sales/Invoice By Customer Group)', 310, 0, NULL, NULL, NULL),
(230, '7f37707341b263a39ef98d8688e92a62', 28, 'Report (Receive Payments)', 315, 1, NULL, NULL, NULL),
(231, '73be9b2e954f39190c67bea544d5bb47', 28, 'Report (Receive Payments By Rep)', 316, 0, NULL, NULL, NULL),
(232, '3e9cfb320536f8965e07d3b47a96d88b', 28, 'Report (Pay Bills)', 605, 1, NULL, NULL, NULL),
(233, 'ccf06cde6fe11554fa6013d8a9de7e93', 46, 'Other (view)', 1, 1, NULL, NULL, NULL),
(234, '9982ead321114c782bcbced49fa65879', 46, 'Other (add)', 2, 1, NULL, NULL, NULL),
(235, 'e832bcd96baddee49f203312c4408038', 46, 'Other (edit)', 3, 1, NULL, NULL, NULL),
(236, 'b1e95a075c627dedd7cec0f5431518c6', 46, 'Other (delete)', 4, 1, NULL, NULL, NULL),
(237, 'f47a3889191647893727eb52ab226fb9', 28, 'Report (Deposit)', 807, 0, NULL, NULL, NULL),
(238, 'e7782b62fdd26027aed469e6469790b1', 28, 'Report (Sales By Item POS)', 301, 1, NULL, NULL, NULL),
(239, '2a6ef276d23bdf0bea1b0cef45d5e51e', 28, 'Report (Sales By Item)', 303, 1, NULL, NULL, NULL),
(240, 'f14e9b05a0896a318492ecf7c257730a', 28, 'Report (Sales By Customer)', 305, 1, NULL, NULL, NULL),
(241, 'e33c3f8ce0a38e8bc901a614c32e50f8', 28, 'Report (Sales By Customer Group)', 306, 0, NULL, NULL, NULL),
(242, '04ad136dac93abf10ffe1c8b23c523b1', 28, 'Report (Invoice POS)', 308, 1, NULL, NULL, NULL),
(243, 'c213b48e1b0de5a3a9cdf380bd8cc122', 28, 'Report (Transfer Order)', 201, 1, NULL, NULL, NULL),
(244, '87312390a0ea3a78d3d46dd43bf93704', 28, 'Report (Sales By Item Type)', 304, 0, NULL, NULL, NULL),
(245, '0bed306de05d768d7ac91bba7b911e8a', 28, 'Report (Customer Address)', 403, 0, NULL, NULL, NULL),
(246, 'ce32c31f2f043d84c1998779760debb4', 15, 'Physical Count (View)', 1, 1, NULL, NULL, NULL),
(247, '3bb70778e31901e01819e83afb03418a', 47, 'Employee (view)', 1, 1, NULL, NULL, NULL),
(248, 'd68602d3e98cb8be8954e06f51d8a131', 47, 'Employee (add)', 2, 1, NULL, NULL, NULL),
(249, '92fb1671051de0906f0512ffaa367f98', 47, 'Employee (edit)', 3, 1, NULL, NULL, NULL),
(250, 'bfac0fea89c977c74bbf50998fa3bad2', 47, 'Employee (delete)', 4, 1, NULL, NULL, NULL),
(251, 'a156b62ab3438bfea03c92dcf1bf337d', 28, 'Report (Inventory Valuation)', 103, 1, NULL, NULL, NULL),
(253, '8f4a68d3ea3f238975204697e0d1f097', 48, 'Purchase Order (View)', 1, 1, NULL, NULL, NULL),
(254, 'c23129a4542b273cdbeef8b355b08f80', 48, 'Purchase Order (Add)', 2, 1, NULL, NULL, NULL),
(255, '844c0f83624e8c5cb9e7e13ba944fba4', 48, 'Purchase Order (Edit)', 3, 1, NULL, NULL, NULL),
(256, 'ccf301fc5ad23e96d435ff2c31ada081', 48, 'Purchase Order (Delete)', 4, 1, NULL, NULL, NULL),
(257, 'aba44347b2ae0224652bd5711b5ed7d3', 28, 'Report (Inventory Activity)', 102, 1, NULL, NULL, NULL),
(258, '14ec188bed43783a3084b477659719b2', 28, 'Report (Inventory Adjustment)', 104, 1, NULL, NULL, NULL),
(259, '2966381e0e35b23e4cc6d72ee79c2616', 40, 'Sales Return (Add Discount)', 8, 1, NULL, NULL, NULL),
(260, 'a657a1fca06593e359d2da06db71c351', 48, 'Purchase Order (Add Service)', 5, 1, NULL, NULL, NULL),
(261, '7603551785bbe308d50c9ae484c9ed7c', 4800000, 'Purchase Order (Add Miscs)', 6, 1, NULL, NULL, NULL),
(262, '45f8cac866edbe960e96906c7cad68e5', 4800000, 'Purchase Order (Add Discount)', 7, 1, NULL, NULL, NULL),
(263, 'abdc6d23be2120baf6f33ec85c8c5bb7', 27, 'Journal Entry (supervisor level) (change status)', 11, 1, NULL, NULL, NULL),
(264, '80ba65621d2f99920c012c7daaf38e12', 28, 'Report (Transfer By Item)', 202, 1, NULL, NULL, NULL),
(265, '55f1790f9c891616511adb3432bec185', 28, 'Report (Total Sales)', 307, 1, NULL, NULL, NULL),
(266, 'f9d9fbc6bc0f50e090903c555f6d071d', 28, 'Report (Open Invoice By Customer Group)', 312, 0, NULL, NULL, NULL),
(267, '1dc739935d5d505ff329eb379431c571', 28, 'Report (Delivery)', 314, 0, NULL, NULL, NULL),
(268, 'ada6164976e075cd8c247c255e14330e', 28, 'Report (Purchase By Item)', 602, 1, NULL, NULL, NULL),
(269, '3ff4764e9a13f170df1bfb3657afea55', 28, 'Report (Check)', 806, 0, NULL, NULL, NULL),
(270, 'ac1bfcf080936fbe8e96259f6ea336bb', 50, 'Reconcile', 1, 1, NULL, NULL, NULL),
(271, '4cd64c4c7d63758a1f47d6414e99a0ae', 49, 'Employee Group (view)', 1, 1, NULL, NULL, NULL),
(272, 'fc147abfbbfc433d94c7e9663686c71b', 49, 'Employee Group (add)', 2, 1, NULL, NULL, NULL),
(273, '1df419b4ee7aebfc42bb1dbc785da393', 49, 'Employee Group (edit)', 3, 1, NULL, NULL, NULL),
(274, '5f416107e485dd4453e6f4954e4ffd51', 49, 'Employee Group (delete)', 4, 1, NULL, NULL, NULL),
(276, '5a292806f91ad41a1f914006dce0f262', 51, 'Fixed Asset', 1, 1, NULL, NULL, NULL),
(277, 'd96978ff239a9a96ebbc33ce88c64c03', 28, 'Report (A/R) (Employee)', 501, 0, NULL, NULL, NULL),
(278, 'd20ae3d17ea751764a76ce06e45fefd6', 28, 'Report (Employee Balance)', 502, 0, NULL, NULL, NULL),
(279, '164a53499e2cb1fb8a9f91b47306fa25', 52, 'Account Closing Date', 1, 1, NULL, NULL, NULL),
(280, '51a3fd8967ecf7f30548be5d3ca4451c', 34, 'Point Of Sales (Add Discount for Product)', 9, 1, NULL, NULL, NULL),
(282, '781c3d2ceb2a937c7c5fe45b75f7cb35', 53, 'Delivery Note (View)', 1, 1, NULL, NULL, NULL),
(284, '9ed27d37718cd16e007f5cac2ac2e22b', 53, 'Delivery Note (Pick)', 2, 1, NULL, NULL, NULL),
(286, 'ed691d30310ebe71b8fa192d96fab40a', 54, 'Vendor Group (view)', 1, 1, NULL, NULL, NULL),
(287, '2122dc747290a2b5853498430ce6e31e', 54, 'Vendor Group (add)', 2, 1, NULL, NULL, NULL),
(288, '6d0ef017a8b1275c98245fae9b081dc8', 54, 'Vendor Group (edit)', 3, 1, NULL, NULL, NULL),
(289, '4f57e19a7ac2c6e55b0990a8eb52dbe4', 54, 'Vendor Group (delete)', 4, 1, NULL, NULL, NULL),
(290, '8dfdafe0486a4104dc48cab5998a4786', 25, 'Sales / Invoice (Edit Price)', 10, 1, NULL, NULL, NULL),
(291, '5f69a55eab1bb2aef705d5b55fc6b877', 40, 'Sales Return (Edit Price)', 9, 1, NULL, NULL, NULL),
(292, '8d75ab676f4f0796cfb9a1245103ae5d', 55, 'Price Type (view)', 1, 1, NULL, NULL, NULL),
(293, '36f535824be6123a5f6d8e0df8a6bb52', 55, 'Price Type (add)', 2, 1, NULL, NULL, NULL),
(294, 'f0d761f54f7ead1f4ea61f0628b37094', 55, 'Price Type (edit)', 3, 1, NULL, NULL, NULL),
(295, 'e30b9af1080b994bef3c553a8732b928', 55, 'Price Type (delete)', 4, 1, NULL, NULL, NULL),
(296, '8db6ffab86ed00f5414db9b180ea32b6', 13, 'Product (Set Packet)', 6, 1, NULL, NULL, NULL),
(297, 'f5e323fdb1b21ccfd2998185f55560a7', 6, 'UoM (Export to Excel)', 5, 1, NULL, NULL, NULL),
(298, '17b067fb9d5fabce626e8c06bd6436bc', 7, 'UoM Conversion (Export to Excel)', 5, 1, NULL, NULL, NULL),
(299, 'd0f44f33a60b2119fa2d1fc0f5db4999', 4, 'Location (Export to Excel)', 5, 1, NULL, NULL, NULL),
(300, '249d60ca580f77dac2b1d5e72a9839ec', 5, 'Warehouse (Export to Excel)', 5, 1, NULL, NULL, NULL),
(301, '0c183ec3269cd648103370c6148d8798', 47, 'Employee (Export to Excel)', 5, 1, NULL, NULL, NULL),
(302, 'bfcf71b3f5137a7d6e56af5153ec8c13', 49, 'Employee Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(303, '7c7956f73723cc2eb4cd6946d249bc89', 39, 'Payment Terms (Export to Excel)', 5, 1, NULL, NULL, NULL),
(304, 'a72ebffb5af1b241e12c4b2c7fbe5ff2', 46, 'Other (Export to Excel)', 5, 1, NULL, NULL, NULL),
(305, 'c3f7318681a400b26b80f7129346ba32', 36, 'Class (Export to Excel)', 5, 1, NULL, NULL, NULL),
(306, 'b6982c85193384a712acf50481535846', 14, 'Customer (Export to Excel)', 5, 1, NULL, NULL, NULL),
(307, '3ba9139a69bd4626a44c45faa9d5c1fe', 12, 'Customer Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(308, '08c7dc603f4839173ffa8045fb26b217', 54, 'Vendor Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(309, 'f9ac796f2932d87ca18bae91d3b9df84', 22, 'Vendor (Export to Excel)', 5, 1, NULL, NULL, NULL),
(310, '4da39460fc80af19d1c5b4db3742b599', 32, 'Section (Export to Excel)', 5, 1, NULL, NULL, NULL),
(311, 'eaa476125d88d1f0f5abcfcb6db3c9df', 33, 'Service (Export to Excel)', 5, 1, NULL, NULL, NULL),
(312, '88f706357b4bd58c416c4cf9ef37f117', 17, 'Product Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(313, '2d9d20dac0e02137f656db9b0e8b33ad', 13, 'Product (Export to Excel)', 7, 1, NULL, NULL, NULL),
(314, '017a2accaded7722b2ff6958e1c7969e', 20, 'Chart of Account Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(315, '7338f66c8eb07b906cb3dc64a66ba8d4', 40, 'Sales Return (View By User)', 2, 1, NULL, NULL, NULL),
(316, '82f330372f73e87540a208165ea710ab', 25, 'Sales / Invoice (View By User)', 2, 1, NULL, NULL, NULL),
(317, '52cdea0a859644823f00041159544f97', 21, 'Purchase Bill (View By User)', 7, 1, NULL, NULL, NULL),
(318, '7d6940ea148bf10f463069332f655f4d', 41, 'Purchase Return (View By User)', 2, 1, NULL, NULL, NULL),
(319, '9dd796aec8f3982d92cea04f603bcf1f', 53, 'Delivery Note (View By User)', 3, 1, NULL, NULL, NULL),
(320, '9037967ceb991acd5509d35e745d6a51', 24, 'Transfer Order (View By User)', 5, 1, NULL, NULL, NULL),
(321, 'a4209ffdee7169294370b76d607206f8', 15, 'Physical Count (View By User)', 6, 1, NULL, NULL, NULL),
(322, 'e753ff8ff5f9dbd63e169d88e04dc534', 15, 'Physical Count (Delete)', 4, 1, NULL, NULL, NULL),
(323, '2efa0097d9508fc4419b115d793bdb38', 15, 'Physical Count (Receive)', 5, 1, NULL, NULL, NULL),
(328, 'c87ef53224710bd79c74a6c1527a567f', 34, 'Point Of Sales (Reprint)', 8, 1, NULL, NULL, NULL),
(329, 'ba7bc884fa64fd3a13f518355133369a', 48, 'Purchase Order (Close)', 8, 1, NULL, NULL, NULL),
(334, 'e2da1d9a353cd5b5c86a845bcd671038', 57, 'Shipment (View)', 1, 1, NULL, NULL, NULL),
(335, 'e0e0b4c3e0543f91a9c20374be45c5fc', 57, 'Shipment (Add)', 2, 1, NULL, NULL, NULL),
(336, '0011b859db6f215b6f5f382e4e6fa4a0', 57, 'Shipment (Edit)', 3, 1, NULL, NULL, NULL),
(337, '36db70beb3d70336c14c1d0165b08311', 57, 'Shipment (Delete)', 4, 1, NULL, NULL, NULL),
(338, '0bc088ce3fca2c3c07e7067d835eea4d', 58, 'Street (View)', 1, 1, NULL, NULL, NULL),
(339, 'c9d4234da900f785e818b8935b23b7c6', 58, 'Street (Add)', 2, 1, NULL, NULL, NULL),
(340, 'efce16565f0b6aa22d42d1fe84935905', 58, 'Street (Edit)', 3, 1, NULL, NULL, NULL),
(341, '4cefb44fbebb390e029e8ab4023aacc6', 58, 'Street (Delete)', 4, 1, NULL, NULL, NULL),
(342, 'bc190bba8b1718d1b4f3cda8f74b85f2', 59, 'Reason (View)', 1, 1, NULL, NULL, NULL),
(343, '8cb1df66c5ed3cbecca8cfecb8a74a0a', 59, 'Reason (Add)', 2, 1, NULL, NULL, NULL),
(344, '0f08d0a376e74176e85dc1c84cac6abc', 59, 'Reason (Edit)', 3, 1, NULL, NULL, NULL),
(345, '4cecf0d35237cbd5f0ca032123456beb', 59, 'Reason (Delete)', 4, 1, NULL, NULL, NULL),
(346, 'a84c0fb621540a53527958c547e36340', 60, 'Place (View)', 1, 1, NULL, NULL, NULL),
(347, 'bfc5a89eb20df92ae3f1e459c4b7e9cd', 60, 'Place (Add)', 2, 1, NULL, NULL, NULL),
(348, '47244343b521c52e70755e6de165b254', 60, 'Place (Edit)', 3, 1, NULL, NULL, NULL),
(349, 'b29ddb6c4486db913d2376c69765a031', 60, 'Place (Delete)', 4, 1, NULL, NULL, NULL),
(350, 'e022f6b95541a983f00ee66ee7c4de6e', 61, 'Position (View)', 1, 1, NULL, NULL, NULL),
(351, 'a208cedd7c89b549fe0e6682dfacc5ab', 61, 'Position (Add)', 2, 1, NULL, NULL, NULL),
(352, 'f691281bbef73af483e59d52fe0763ea', 61, 'Position (Edit)', 3, 1, NULL, NULL, NULL),
(353, '9cd6c256f39b3b793068c302a9d77a21', 61, 'Position (Delete)', 4, 1, NULL, NULL, NULL),
(374, '92ca9ef1ecbcdac77ef2e8ad087e9c9e', 68, 'Quotataion (View)', 1, 1, NULL, NULL, NULL),
(375, 'e1d41a2897d8ff1483b1400a96bec11b', 68, 'Quotataion (Add)', 2, 1, NULL, NULL, NULL),
(376, 'ea07d08a0a16e7f47aea153f7b4ffda7', 68, 'Quotataion (Edit)', 3, 1, NULL, NULL, NULL),
(377, 'd8494c1bb8ddc2df4b0c8247b2961077', 68, 'Quotataion (Delete)', 4, 1, NULL, NULL, NULL),
(378, 'e07b60acb3208a839302f4f017e74f3b', 68, 'Quotataion (View By User)', 5, 1, NULL, NULL, NULL),
(379, 'd429d4e590c0495f040f361db8e355ed', 68, 'Quotataion (Open)', 6, 1, NULL, NULL, NULL),
(380, 'f94c4df285c7b031bbe1753b4d8e9842', 68, 'Quotataion (Close)', 7, 1, NULL, NULL, NULL),
(381, 'd69c4b47e775ea672e004c63c6a3a7b8', 69, 'Sales Order (View)', 1, 1, NULL, NULL, NULL),
(382, 'ab4128de778bcdc15e68576eccba5b35', 69, 'Sales Order (Add)', 2, 1, NULL, NULL, NULL),
(383, '02cbdf008416eb88d4692d0b0e3ad828', 69, 'Sales Order (Edit)', 3, 1, NULL, NULL, NULL),
(384, '2cfbf14c81c8572d1db6262aef95e0bc', 69, 'Sales Order (Delete)', 4, 1, NULL, NULL, NULL),
(385, 'e7a5cd3811dfe07f922589fd8ffae4d9', 69, 'Sales Order (View By User)', 5, 1, NULL, NULL, NULL),
(386, '6186603cefb6bff85d9c26989ed77c01', 69, 'Sales Order (Action)', 6, 1, NULL, NULL, NULL),
(387, '7aef4af714745fff3233b6811a9b588c', 69, 'Sales Order (Close)', 7, 1, NULL, NULL, NULL),
(388, '62db6a742437fac5810080b3430824c3', 70, 'Request (View)', 1, 1, NULL, NULL, NULL),
(389, 'bab582d6cfe2bb650154bcd91452bbef', 70, 'Request (Add)', 2, 1, NULL, NULL, NULL),
(390, '3325d2d82fd618443647a6cfb0cf3597', 70, 'Request (Edit)', 3, 1, NULL, NULL, NULL),
(391, 'd75339ace8208fbe73d14c97dcac80e7', 70, 'Request (Delete)', 4, 1, NULL, NULL, NULL),
(392, 'f3bef97cab69ee760711215ae462b615', 70, 'Request (Print Invoice)', 5, 1, NULL, NULL, NULL),
(393, 'd072499b5c4815cff1deed41819c0b2b', 70, 'Request (View By User)', 6, 1, NULL, NULL, NULL),
(394, 'a21af48033264316a3a25fd6e718cfaf', 25, 'Sales / Invoice (Approve)', 11, 1, NULL, NULL, NULL),
(395, 'fe7791094e5f579a4200f53fdd7b1ab5', 53, 'Delivery Note (Reprint)', 4, 1, NULL, NULL, NULL),
(396, 'a3a090ddbcac8a841c3cf7086f1b02fb', 40, 'Sales Return (Receive Products)', 10, 1, NULL, NULL, NULL),
(397, '0d7b4ea9540b5b3f464036e03a828df5', 48, 'Purchase Order (Active)', 9, 1, NULL, NULL, NULL),
(398, '8421e27defc2eab7b63b791981e2b08f', 21, 'Purchase Bill (Discount)', 6, 1, NULL, NULL, NULL),
(399, 'e12a03fd372235bd5956c18d79045d69', 41, 'Purchase Return (Receive)', 8, 1, NULL, NULL, NULL),
(416, 'cffddb46791d89f2e660b782558098ea', 47, 'Employee (Deactivate/Active)', 6, 1, NULL, NULL, NULL),
(417, 'c66e7c89d708c916634fe1aa5c6a5e7d', 68, 'Quotataion (Add Service)', 2, 1, NULL, NULL, NULL),
(418, '3b916bbb66441b2e04963f22ae4613b3', 68, 'Quotataion (Add Misc)', 2, 1, NULL, NULL, NULL),
(419, 'a49a232c5201ae5ed16310279c85aef2', 69, 'Sales Order (Add Service)', 2, 1, NULL, NULL, NULL),
(420, 'f672ba780d66d95ab02f017d138887b6', 69, 'Sales Order (Add Misc)', 2, 1, NULL, NULL, NULL),
(421, '5251e913a0cbdf615e6bc9bbe120ec51', 68, 'Quotataion (Edit Price)', 8, 1, NULL, NULL, NULL),
(422, 'f390d5a74965e5dbaed95a36dbc6805c', 69, 'Sales Order (Edit Price)', 8, 1, NULL, NULL, NULL),
(423, '1de7a5413d3f91d901088a03399d5609', 69, 'Sales Order (Discount)', 9, 1, NULL, NULL, NULL),
(424, '534e983dbcdc48435df605ee504302ff', 68, 'Quotataion (Discount)', 9, 1, NULL, NULL, NULL),
(425, '3f7fbac275ab8ca3ab4958f38bbfb521', 13, 'Product (Set Cost)', 8, 1, NULL, NULL, NULL),
(426, 'fe93717dae9eb0d197bd5e4a7a3e2cbc', 48, 'Purchase Order (Edit Unit Cost)', 10, 1, NULL, NULL, NULL),
(427, 'f8291c4e4d68144be27e0bd27bf69a41', 71, 'VAT Setting (View)', 1, 1, NULL, NULL, NULL),
(428, '34fb68461fac9d411cb4334d68da80d8', 71, 'VAT Setting (Edit)', 2, 1, NULL, NULL, NULL),
(429, 'b8cd4c1f2f5bc9d2abb584aa58dec14c', 3, 'Group (Add)', 2, 1, NULL, NULL, NULL),
(430, '9d671baf73f2420bd53b1c401753a1ea', 3, 'Group (Edit)', 3, 1, NULL, NULL, NULL),
(431, '5eb8e1552959ad2df06d83e6929e0edb', 3, 'Group (Delete)', 4, 1, NULL, NULL, NULL),
(432, '9e6fe9172f71200d9219775f108a9748', 2, 'User (Add)', 2, 0, NULL, NULL, NULL),
(433, 'd391f7120097ed705e9d1b6a239ce50d', 2, 'User (Edit)', 3, 1, NULL, NULL, NULL),
(434, '758f7e09f47bdc63b7690119ca9d97f5', 2, 'User (Delete)', 4, 0, NULL, NULL, NULL),
(435, '02a49f6d3fa8aade949455f1318a8924', 72, 'Terms & Condition (View)', 1, 1, NULL, NULL, NULL),
(436, 'afcd933748033e1a0b99dd02c2a63783', 72, 'Terms & Condition (Add)', 2, 1, NULL, NULL, NULL),
(437, '8001a72e3605b92a4b7a0e237d521159', 72, 'Terms & Condition (Edit)', 3, 1, NULL, NULL, NULL),
(438, 'd99403a7245ccfa7bf65eaafb176e3f4', 72, 'Terms & Condition (Delete)', 4, 1, NULL, NULL, NULL),
(439, '4ce55cde8696f4e21ec63c9472be9bf3', 73, 'Terms & Condition Type (View)', 1, 1, NULL, NULL, NULL),
(440, '833be7d066068c8b7355c2e5fa58723a', 73, 'Terms & Condition Type (Add)', 2, 1, NULL, NULL, NULL),
(441, '4f70f3a698b67a44232880a422ed8142', 73, 'Terms & Condition Type (Edit)', 3, 1, NULL, NULL, NULL),
(442, '9d87dc38762e89f3d987dd745f123b70', 73, 'Terms & Condition Type (Delete)', 4, 1, NULL, NULL, NULL),
(443, '8058ae423e1c399c9f17634decfece6c', 74, 'Terms & Condition Apply (View)', 1, 1, NULL, NULL, NULL),
(444, '5b40078b17b7014edcab3cfd2b5af1f6', 74, 'Terms & Condition Apply (Add)', 2, 1, NULL, NULL, NULL),
(445, '7509e0cc656306796a6c6f9a2d40a66d', 74, 'Terms & Condition Apply (Edit)', 3, 1, NULL, NULL, NULL),
(446, '21b2936b644af88f4236964eb3c7c68b', 74, 'Terms & Condition Apply (Delete)', 4, 1, NULL, NULL, NULL),
(447, '45b83637c3ff7d51abdf5f9ed406746f', 75, 'Customer Contact (view)', 1, 1, NULL, NULL, NULL),
(448, 'ebb1f18d4a3f19aec575632b0d83ee36', 75, 'Customer Contact (Add)', 2, 1, NULL, NULL, NULL),
(449, 'f98a12bbf623dc2b65f56c0de6e6e0e3', 75, 'Customer Contact (Edit)', 3, 1, NULL, NULL, NULL),
(450, '0235f1ffe587f54c36e48793a2c2901d', 75, 'Customer Contact (Delete)', 4, 1, NULL, NULL, NULL),
(451, '8fdf2160a212d5f5f960ccfbc88e77ea', 75, 'Customer Contact (Export to Excel)', 5, 1, NULL, NULL, NULL),
(452, '5ba15512804e302c61474ae7622f42cf', 68, 'Quotataion (Edit Terms & Condition)', 10, 1, NULL, NULL, NULL),
(453, '382406b3870f1be4fb53eb60c5bf7b30', 68, 'Quotataion (Total Discount)', 11, 1, NULL, NULL, NULL),
(458, '98d8fd904ece3988c0383f1d87ce9b44', 69, 'Sales Order (Edit Terms & Condition)', 10, 1, NULL, NULL, NULL),
(459, '3e68d33a3c1c5caf5c5b18d193483549', 69, 'Sales Order (Total Discount)', 11, 1, NULL, NULL, NULL),
(460, '99df4b470f5b7ff6d60548e43906d643', 48, 'Purchase Order (Edit Term & Conditions)', 11, 1, NULL, NULL, NULL),
(461, 'c45de2436b3cdc6592911584105cc434', 25, 'Sales / Invoice (Edit Total Discount)', 12, 1, NULL, NULL, NULL),
(462, '37e09a2f853a153f89823d1516c917c0', 25, 'Sales / Invoice (Edit Terms & Condition)', 13, 1, NULL, NULL, NULL),
(463, '631ead420fd7d9af85a851cb5b73ae24', 71, 'VAT Setting (Add)', 3, 1, NULL, NULL, NULL),
(464, '85cb9b318154245cd98453a02f12fcfa', 71, 'VAT Setting (Delete)', 4, 1, NULL, NULL, NULL),
(465, 'cd1547357f9c1f00dd5f3c27fe508a1e', 21, 'Purchase Bill (Add Service)', 8, 1, NULL, NULL, NULL),
(466, '423b99067c49514780a726def8095da1', 21, 'Purchase Bill (Total Discount)', 9, 1, NULL, NULL, NULL),
(467, '2b3f2cae9cc195616c1dd7e92e15b589', 68, 'Quotataion (Approve)', 12, 1, NULL, NULL, NULL),
(468, '48eec68fdad45b1f108230eb358b30dd', 21, 'Purchase Bill (Close All Items are Service)', 10, 1, NULL, NULL, NULL),
(469, 'e48032132e299866f30c5e36ae76810f', 77, 'Currency Setting (View)', 1, 1, NULL, NULL, NULL),
(470, 'c9f73573f51dac022ad3ebb43c84dcca', 77, 'Currency Setting (Add)', 2, 1, NULL, NULL, NULL),
(471, '3c5d989a1ef1a957c5313ac0310b33f7', 77, 'Currency Setting (Edit)', 3, 1, NULL, NULL, NULL),
(472, 'b3de8457683daded1859d99b81b09e6c', 77, 'Currency Setting (Delete)', 4, 1, NULL, NULL, NULL),
(473, '45b168dbf368db1748dc620da956809d', 78, 'Branch Currency (View)', 1, 1, NULL, NULL, NULL),
(474, '8fe3a59670b30d7df49ee8b1c5614c61', 78, 'Branch Currency (Add)', 2, 1, NULL, NULL, NULL),
(475, 'eceb2301e82b0bd2b46bb3c0c6e24e22', 78, 'Branch Currency (Edit)', 3, 1, NULL, NULL, NULL),
(476, 'c2881620d08d46f22e7e129d125bf6c5', 78, 'Branch Currency (Delete)', 4, 1, NULL, NULL, NULL),
(477, '909fe170a97793bd4fcfcdaf206969cb', 78, 'Branch Currency (Apply To POS)', 5, 1, NULL, NULL, NULL),
(478, '5e5d537280239411ab192a152dad335a', 79, 'Sales Target (View)', 1, 1, NULL, NULL, NULL),
(479, '30c6fc9e1004d6be4e9f88ee9865a613', 79, 'Sales Target (Add)', 2, 1, NULL, NULL, NULL),
(480, '7f9dfabacc6bb548d37e3cbb71d38994', 79, 'Sales Target (Edit)', 3, 1, NULL, NULL, NULL),
(481, '55718011831c21726a5122340314de64', 79, 'Sales Target (Delete)', 4, 1, NULL, NULL, NULL),
(482, '68e707594fd9721e47f4dbaaf0a1f136', 79, 'Sales Target (Approve)', 4, 1, NULL, NULL, NULL),
(483, '58999df405d146bd519ebfee089830a1', 69, 'Sales Order (Approve)', 12, 1, NULL, NULL, NULL),
(484, '8722316b992b37718581a3b19c527f64', 2, 'User (Edit Profile)', 5, 1, NULL, NULL, NULL),
(485, '82795f80df68913b6002be08cad1b8f6', 80, 'Total Changing Unit Cost & Price', 1, 1, NULL, NULL, NULL),
(486, '6e2b07aefbdc078de58eafc197aeb26a', 80, 'Changing of Unit Cost', 2, 1, NULL, NULL, NULL),
(487, '9a67b9c36d7421ac268a42a8823dd582', 81, 'Request Stock (Issued)', 1, 1, NULL, NULL, NULL),
(489, 'c1cfcffc576cade5a7c4e11a76259861', 28, 'Report (Sales Top Item)', 304, 1, NULL, NULL, NULL),
(490, '7e98a359fd2868d4b915933f2127036e', 13, 'Product (View Cost)', 9, 1, NULL, NULL, NULL),
(491, 'd0a48eec3c385db53388bd0f0a9694f0', 28, 'Report (Sales Top/Bottom Customer)', 305, 1, NULL, NULL, NULL),
(492, '357b940f34a5cf9236a63c8511aeac98', 28, 'Report (Sales By Rep)', 307, 0, NULL, NULL, NULL),
(493, 'a0d98ac0b25abd584a2eae4c78d9a632', 28, 'Report (Invoices By Rep)', 307, 0, NULL, NULL, NULL),
(494, '0673c8f38d428b54c54fc5de5f295879', 28, 'Report (Statement)', 307, 1, NULL, NULL, NULL),
(495, 'a33422390bedc6246496377139fddf60', 28, 'Report (Statement By Rep)', 307, 0, NULL, NULL, NULL),
(496, '16b11c61eeba7b5c8fdc59bbb27a9cc7', 28, 'Report (Customer Balance By Invoice)', 307, 1, NULL, NULL, NULL),
(497, 'dd97ae8a2acfa9c942b7aeba109e1c55', 28, 'Report (Vendor Balance By Invoice)', 307, 1, NULL, NULL, NULL),
(498, '6ba5db38a4fae0f48fc74ae64ace9151', 28, 'Report (Audi Trail)', 307, 0, NULL, NULL, NULL),
(499, 'b7cd08091f57d8025e39c2e3373d1b61', 108, 'Physical Count (Issue)', 1, 1, NULL, NULL, NULL),
(500, '10587930e682d46036939c2fc3cd7522', 83, 'Transfer Order (Not Yet Receive)', 1, 0, NULL, NULL, NULL),
(501, '337d37b91e4524e17c4726bf77f1524a', 84, 'Quotation (Not Yet Approve)', 1, 0, NULL, NULL, NULL),
(502, '6de9851bd8eb2fd7219a7137026effd9', 85, 'Sales Order (Not Yet Approve)', 1, 0, NULL, NULL, NULL),
(503, '34d7c2960b2f3d7eb8204ed89560e568', 86, 'Sales/Invoice (Not Yet Delivery)', 1, 0, NULL, NULL, NULL),
(504, '53c8642a2ef3dd1440f4183cbb29f2a8', 87, 'Sales Return (Issued)', 1, 0, NULL, NULL, NULL),
(505, '325af30fe9fc2f6388c7651aeb97b606', 88, 'Purchase Bill (Not Yet Receive)', 1, 0, NULL, NULL, NULL),
(506, '77184a0631c7eb1bc42f79855e5b974e', 28, 'Report (Quotation)', 317, 0, NULL, NULL, NULL),
(507, '1aef08e19965750f7dfcd56d8ffb8661', 28, 'Report (Quotation)', 1, 0, NULL, NULL, NULL),
(508, 'eefa9c9a38144100f4e7243387c878f4', 28, 'Report (Sales Order)', 2, 0, NULL, NULL, NULL),
(509, '3581f3537c26638540fb0209b69481cc', 80, 'Products Reorder Level', 3, 1, NULL, NULL, NULL),
(510, '9ed0a750cd0df4a621b26cfab41f54b5', 80, 'Products Expire Date', 3, 1, NULL, NULL, NULL),
(511, '8a3117b25b3bf2d85f8236b4a29255c2', 89, 'Branch (View)', 1, 1, NULL, NULL, NULL),
(512, '602a2e7484fa9c294c8f4d2a09529d45', 89, 'Branch (Add)', 2, 1, NULL, NULL, NULL),
(513, 'dd8efad2147f85d5d4cc79131040129f', 89, 'Branch (Edit)', 3, 1, NULL, NULL, NULL),
(514, '6da9cf5a41d969d09aa4d306cb48a14d', 89, 'Branch (Delete)', 4, 1, NULL, NULL, NULL),
(515, 'fd9bb8bebb941f02fa67ef98eb61e197', 90, 'Branch Type (View)', 1, 1, NULL, NULL, NULL),
(516, 'cd2ce23ee207229a3b884e6741859e7f', 90, 'Branch Type (Add)', 2, 1, NULL, NULL, NULL),
(517, '30e6f643de3ee039e0f3c5f3738cedec', 90, 'Branch Type (Edit)', 3, 1, NULL, NULL, NULL),
(518, '8c10226fb0d72f8299b0513db56a024e', 90, 'Branch Type (Delete)', 4, 1, NULL, NULL, NULL),
(519, '8a611ab64e1070a749ccbb2eb4231de1', 91, 'Customer Consignment (View)', 1, 1, NULL, NULL, NULL),
(520, '1b4ca124f95b6afaf0dc7e3a965bf820', 91, 'Customer Consignment (Add)', 2, 1, NULL, NULL, NULL),
(521, '3284601006f8d20d91b76acc8be88355', 91, 'Customer Consignment (Edit)', 3, 1, NULL, NULL, NULL),
(522, '0eb8564b188793ba071a9a6b80858d31', 91, 'Customer Consignment (Delete)', 4, 1, NULL, NULL, NULL),
(523, 'fade42f36427bd3cc90b8d9c84e61e31', 91, 'Customer Consignment (Receive)', 5, 1, NULL, NULL, NULL),
(524, '8f10a342b55610b41f6e083003bef864', 91, 'Customer Consignment (View By User)', 6, 1, NULL, NULL, NULL),
(525, '370c9f9057d9e548d8372d2746de4c57', 91, 'Customer Consignment (Print)', 7, 1, NULL, NULL, NULL),
(526, 'f4bbaf39edce9caaac814137fa36c880', 91, 'Customer Consignment (Edit Term Condition)', 8, 1, NULL, NULL, NULL),
(527, 'b4bd265e03a3c93caeddb90f6357cc15', 92, 'Customer Return Consignment (View)', 1, 1, NULL, NULL, NULL),
(528, '7fa0b7abcbdf9ca74840b0fb5b9e9e95', 92, 'Customer Return Consignment (Add)', 2, 1, NULL, NULL, NULL),
(529, '29f7a95c01b50f9c8ea8d52282e2b995', 92, 'Customer Return Consignment (Edit)', 3, 1, NULL, NULL, NULL),
(530, '45b1a0da88c42057e9d037fe2f349ef1', 92, 'Customer Return Consignment (Void)', 4, 1, NULL, NULL, NULL),
(531, '4439af63a9ec6d55b63f1c635fcce4d3', 92, 'Customer Return Consignment (Reprint Invoice)', 5, 1, NULL, NULL, NULL),
(532, '13d7a2bea59b47a2a327cc5207fc23f6', 92, 'Customer Return Consignment (Receive)', 6, 1, NULL, NULL, NULL),
(533, 'c4bd530aface3db11dfdec663c32d1ab', 92, 'Customer Return Consignment (View By User)', 7, 1, NULL, NULL, NULL),
(534, '9335ef03f23e80a9e95c5c8718bdf2a4', 93, 'Vendor Consignment (View)', 1, 1, NULL, NULL, NULL),
(535, '5dd0960efb9fc746374bf230165dc6d2', 93, 'Vendor Consignment (Add)', 2, 1, NULL, NULL, NULL),
(536, 'c8362aebdd03c7c9dcf4f5d2478c311e', 93, 'Vendor Consignment (Edit)', 3, 1, NULL, NULL, NULL),
(537, 'e52dd4f561dba3b552c643c15e1b766b', 93, 'Vendor Consignment (Void)', 4, 1, NULL, NULL, NULL),
(538, 'abe282488d19caab8ad57b70a41aabaa', 93, 'Vendor Consignment (Reprint Invoice)', 5, 1, NULL, NULL, NULL),
(539, 'b528b1304439279c8366219355d1c82e', 93, 'Vendor Consignment (Receive)', 6, 1, NULL, NULL, NULL),
(540, '0068893acf22104ba02e4e1b49c06e59', 93, 'Vendor Consignment (View By User)', 7, 1, NULL, NULL, NULL),
(541, '67b4ae9c0b965eaf90611a16ca9c1a62', 94, 'Vendor Consignment Return (View)', 1, 1, NULL, NULL, NULL),
(542, '676349212068c1eab25eb79d2b01bf6c', 94, 'Vendor Consignment Return (Add)', 2, 1, NULL, NULL, NULL),
(543, '1fbff630790a4140d9acf7a072552232', 94, 'Vendor Consignment Return (Edit)', 3, 1, NULL, NULL, NULL),
(544, 'f218cbb15ae049b89d3a26ad1698f8e1', 94, 'Vendor Consignment Return (Void)', 4, 1, NULL, NULL, NULL),
(545, 'd0b3abea9ff42affe5490ee40eec2e1a', 94, 'Vendor Consignment Return (Reprint Invoice)', 5, 1, NULL, NULL, NULL),
(546, '17abd0d301d901c7860772c5406ee119', 94, 'Vendor Consignment Return (Receive)', 6, 1, NULL, NULL, NULL),
(547, '8df8f9a2f4475d1a8600c1ec10442e79', 94, 'Vendor Consignment Return (View By User)', 7, 1, NULL, NULL, NULL),
(548, '0cf0ee53ac4369907183a06087888c09', 95, 'Landed Cost Type (View)', 1, 1, NULL, NULL, NULL),
(549, '7b31291c50415ffa766eaadee6c9cd11', 95, 'Landed Cost Type (Add)', 2, 1, NULL, NULL, NULL),
(550, 'a70b8e5e365092f424e4ba7fea0ba9ed', 95, 'Landed Cost Type (Edit)', 3, 1, NULL, NULL, NULL),
(551, '70b42e08503932e4ade9e2cdd7c39dfb', 95, 'Landed Cost Type (Delete)', 4, 1, NULL, NULL, NULL),
(552, '0703562e7f9aec6b69dbf0d395287558', 96, 'Landed Cost (View)', 1, 1, NULL, NULL, NULL),
(553, 'd5fc21bf647e8dd8adbefa947bb62d3e', 96, 'Landed Cost (Add)', 2, 1, NULL, NULL, NULL),
(554, 'd48ff6b04000f85d2139c15fdcb3d987', 96, 'Landed Cost (Edit)', 3, 1, NULL, NULL, NULL),
(555, '2fb5bb626d75242ed364c389dd1ec680', 96, 'Landed Cost (Void)', 4, 1, NULL, NULL, NULL),
(556, '8bed5643569db0c5cd799409d48780e2', 96, 'Landed Cost (Aging)', 5, 1, NULL, NULL, NULL),
(557, '10d128ff77550b120a166504f9918c04', 96, 'Landed Cost (View By User)', 6, 1, NULL, NULL, NULL),
(558, '6b0098afd26b9e168aec90b6e0d41a90', 96, 'Landed Cost (Close)', 7, 1, NULL, NULL, NULL),
(559, '2ad773dcd814481d620cabef94e85898', 97, 'Warehouse Type (View)', 1, 1, NULL, NULL, NULL),
(560, '90d79c28a80f226e203d5cabaa9aa7fc', 97, 'Warehouse Type (Add)', 2, 1, NULL, NULL, NULL),
(561, 'cca9aa12878ce6b66be5843bbeb9a8a7', 97, 'Warehouse Type (Edit)', 3, 1, NULL, NULL, NULL),
(562, '87697fdc9c66ff2568583c724be385eb', 97, 'Warehouse Type (Delete)', 4, 1, NULL, NULL, NULL),
(563, 'b21f782e5df2731768ecc43e36b77250', 97, 'Warehouse Type (Export to Excel)', 5, 1, NULL, NULL, NULL),
(564, '92a9580669e45d18f2de9e02b9584214', 4, 'Location (Change Status)', 6, 1, NULL, NULL, NULL),
(565, 'dd037f20b36fb0e098547c8eef022f0f', 98, 'E-Commercer', 6, 1, NULL, NULL, NULL),
(566, 'd6fc2b20c830f36dd98e772d9045d78c', 99, 'SYNC Monitoring (View)', 1, 1, NULL, NULL, NULL),
(567, 'bbbd94c0e045492d9ba378c93c945fab', 13, 'Product (View Activity By Graph)', 1, 1, 3, '15.000', '(Admin Modules)'),
(568, '203879e9149f416b2e338367e28471c1', 13, 'Product (View Activity Sales/Purchase By Graph)', 1, 1, 3, '15.000', '(Admin Modules)'),
(569, '1a4ca9a4901e22aa243cc66e20529a30', 28, 'Report (Inventory Customer Consignment)', 102, 0, NULL, NULL, NULL),
(570, '67edeab63f48de063fe15429a8fe4c22', 28, 'Report (Request Stock)', 201, 0, NULL, NULL, NULL),
(571, 'f2b37696d743de701688949bbf587100', 28, 'Report (Request Stock By Item)', 201, 0, NULL, NULL, NULL),
(572, '06c12e28566ac669feb1024b68e04848', 100, 'Sales / Invoice Consignment (View)', 1, 1, NULL, NULL, NULL),
(573, 'c62756f85bb1225d12d71457b09f3061', 100, 'Sales / Invoice Consignment (Add)', 2, 1, NULL, NULL, NULL),
(574, 'fe82f8dbe7124506386bc907c5be40cd', 100, 'Sales / Invoice Consignment (Aging)', 4, 1, NULL, NULL, NULL),
(575, 'f44284b85eb95d63dbfbb280f069b44f', 100, 'Sales / Invoice Consignment (Void)', 5, 1, NULL, NULL, NULL),
(576, '748d4b45412d02dfbca4003c60a43318', 100, 'Sales / Invoice Consignment (Add Misc.)', 7, 1, NULL, NULL, NULL),
(577, 'cb7445717680b1d1f4538dd1d47ed7ee', 100, 'Sales / Invoice Consignment (Add Service)', 6, 1, NULL, NULL, NULL),
(578, '3739b4d3a133cca61b4a78922d9580b0', 100, 'Sales / Invoice Consignment (Print Invoice)', 9, 1, NULL, NULL, NULL),
(579, '4df4bd917dc0ec6144b93026336ea9e4', 100, 'Sales / Invoice Consignment (Add Discount)', 8, 1, NULL, NULL, NULL),
(580, '6fa0bdf443d61809773d831bfc163f81', 100, 'Sales / Invoice Consignment (Edit)', 3, 1, NULL, NULL, NULL),
(581, '0041f3b176459594f74e91ebc2c02623', 100, 'Sales / Invoice Consignment (Edit Price)', 10, 1, NULL, NULL, NULL),
(582, '19e75be7cf181b02af2dea9257a55dee', 100, 'Sales / Invoice Consignment (View By User)', 2, 1, NULL, NULL, NULL),
(583, 'e410241bef4a1169bae135172809d862', 100, 'Sales / Invoice Consignment (Pick)', 11, 1, NULL, NULL, NULL),
(584, '3585ef318e5a6ae221a2375c5a904d0d', 100, 'Sales / Invoice Consignment (Edit Total Discount)', 12, 1, NULL, NULL, NULL),
(585, '7e56efe46fbecec11a6571450586eb86', 100, 'Sales / Invoice Consignment (Edit Terms & Condition)', 13, 1, NULL, NULL, NULL),
(586, '53b433f2cd10f8e73a785573dde48925', 101, 'Vendor Contact (View)', 1, 1, NULL, NULL, NULL),
(587, 'be371792271567397a62177c6a9b8f5b', 101, 'Vendor Contact (Add)', 2, 1, NULL, NULL, NULL),
(588, '71157e9ae99d5e00339fd71a15522ccb', 101, 'Vendor Contact (Edit)', 3, 1, NULL, NULL, NULL),
(589, '130571bd155c5f3c52c489543f3d2d96', 101, 'Vendor Contact (Delete)', 4, 1, NULL, NULL, NULL),
(590, '88437dfe2b0d89afe4c8b1d24f6cd31c', 101, 'Vendor Contact (Export to Excel)', 5, 1, NULL, NULL, NULL),
(591, '4a9f33e85f2e7d8eb1c0fd78296a157e', 4, 'Location (View Product in Location)', 7, 1, NULL, NULL, NULL),
(592, '23caa801b70f7657d925749149c5a6f9', 5, 'Warehouse (View Product in Warehouse)', 6, 1, NULL, NULL, NULL),
(593, '66bb08c553e78eac0398fc57e892a37f', 24, 'Transfer Order (Approval)', 6, 1, NULL, NULL, NULL),
(594, '4c92cf43dd2d2d13f2ed1a8a0bad4191', 102, 'Warehouse Map (View)', 1, 1, NULL, NULL, NULL),
(595, 'ad2fc72a11a0fe57c96951634b19d525', 34, 'Point of Sales (Add Customer)', 1, 1, NULL, NULL, NULL),
(596, 'fe100d2954c18500d5370e8601224956', 34, 'Point of Sales (Add Product)', 1, 1, NULL, NULL, NULL),
(597, '62af507ac39b382c80d350193ce937c7', 103, 'General Setting (Set Up)', 1, 1, NULL, NULL, NULL),
(598, '31a5e0369db47a9144b6444392e55bb0', 104, 'ShiftControl (View)', 1, 1, NULL, NULL, NULL),
(599, 'b72fb0523a3f7d7595cd75cfb9f848b7', 105, 'Collect Shift By User (View)', 1, 1, NULL, NULL, NULL),
(600, '2c2db6d26212cd09b5a3f6dd015d5da5', 28, 'Report (POS Shift Control)', 1, 1, NULL, NULL, NULL),
(601, '5f5a3c145f8c0a1ba9e317276bb8f7b5', 28, 'Report (POS Collect Shift By User)', 2, 1, NULL, NULL, NULL),
(602, '7693974bc2b9752cb1f5c4ca10dac2d6', 108, 'Total Sales By Graph', 2, 1, NULL, NULL, NULL),
(603, '4d76926e7b4157338f42126cfc4292ca', 106, 'Color (View)', 1, 1, NULL, NULL, NULL),
(604, '14f223ca91cd8f89361c0813caf36147', 106, 'Color (Add)', 2, 1, NULL, NULL, NULL),
(605, 'c488d78c838b2ec13913d896193589af', 106, 'Color (Edit)', 3, 1, NULL, NULL, NULL),
(606, 'affb7b3d16ebedb3ceaf342f9975c0cf', 106, 'Color (Delete)', 4, 1, NULL, NULL, NULL),
(607, 'a7c3dd6bc4b576a0307609b532b18ea4', 107, 'Cash Expense (View)', 1, 1, NULL, NULL, NULL),
(608, 'f1d7bfce31cf31fd48e72b8e5999b7bc', 107, 'Cash Expense (Add)', 2, 1, NULL, NULL, NULL),
(609, '744640b87e2858a8426be4205692b47f', 107, 'Cash Expense (Edit)', 3, 1, NULL, NULL, NULL),
(610, '3fdc0eec5e22bd07cfcfb9eddc86c94a', 107, 'Cash Expense (Delete)', 4, 1, NULL, NULL, NULL),
(611, 'eac373085adce947fb94920aa36855f6', 108, 'Expense (Graph)', 1, 1, NULL, NULL, NULL),
(612, '4925850b85a02edab357adb05b9defd4', 108, 'Sales Top 10 Items (Graph)', 1, 1, NULL, NULL, NULL),
(613, '917af52b3340f970e429a875baf99d27', 108, 'Profit & Loss (Graph)', 1, 1, NULL, NULL, NULL),
(614, '2db49599f0c041a29aaa9f5700accf29', 108, 'Total Receivables', 1, 1, NULL, NULL, NULL),
(615, '428f197ebb6d834be81b0a5ffd180534', 108, 'Total Payables', 1, 1, NULL, NULL, NULL),
(616, '18599a1bdab5840a3e723a7aae8a8617', 109, 'Inventory Sales Mix (View)', 1, 1, NULL, NULL, NULL);
INSERT INTO `modules` (`id`, `sys_code`, `module_type_id`, `name`, `ordering`, `status`, `type`, `price`, `description`) VALUES
(617, 'e3201c8aa9a3b73116e8064b2141009d', 109, 'Inventory Sales Mix (Add)', 2, 1, NULL, NULL, NULL),
(618, '000e26a4034d1711113bf6091ece0207', 109, 'Inventory Sales Mix (Edit)', 3, 1, NULL, NULL, NULL),
(619, '235aac546ae1d2392f377fb7044e36a5', 109, 'Inventory Sales Mix (Delete)', 4, 1, NULL, NULL, NULL),
(620, 'b13be322dea96fb335930fc48d0f64a0', 109, 'Inventory Sales Mix (Approval)', 5, 1, NULL, NULL, NULL),
(621, '937087b086f238c30e27fac51d243af4', 34, 'Point Of Sales (Edit Price)', 9, 1, NULL, NULL, NULL),
(622, '15fa268fa7442cc187163fdd223dd470', 110, 'Brand (View)', 1, 1, NULL, NULL, NULL),
(623, '3a2dc54eaf9f4b1c56c88666004399ca', 110, 'Brand (Add)', 2, 1, NULL, NULL, NULL),
(624, '8151144303a36dc4535f93107c3104c1', 110, 'Brand (Edit)', 3, 1, NULL, NULL, NULL),
(625, '2029a28742eab4d6008f68bec5659e3a', 110, 'Brand (Delete)', 4, 1, NULL, NULL, NULL),
(626, NULL, 111, 'Patient (View)', 1, 1, NULL, NULL, NULL),
(627, NULL, 111, 'Patient (Add)', 2, 1, NULL, NULL, NULL),
(628, NULL, 111, 'Patient (Edit)', 3, 1, NULL, NULL, NULL),
(629, NULL, 111, 'Patient (Delete)', 4, 1, NULL, NULL, NULL),
(630, NULL, 111, 'Patient (Return-Patient)', 5, 1, NULL, NULL, NULL),
(631, NULL, 112, 'Cashier (Check Out)', 1, 1, NULL, NULL, NULL),
(632, NULL, 112, 'Cashier (Print Invoice Detail)', 2, 1, NULL, NULL, NULL),
(633, NULL, 112, 'Cashier (Pint Invoice Vat)', 3, 1, NULL, NULL, NULL),
(634, NULL, 113, 'Dashboard (Cashier)', 1, 1, NULL, NULL, NULL),
(635, NULL, 114, 'Dashboard (Queue Doctor)', 1, 1, NULL, NULL, NULL),
(636, NULL, 114, 'Tab Consultation (Doctor)', 2, 1, NULL, NULL, NULL),
(637, NULL, 115, 'Labo (View)', 1, 1, NULL, NULL, NULL),
(638, NULL, 116, 'Labo Item (view)', 1, 1, NULL, NULL, NULL),
(639, NULL, 116, 'Labo Item (add)', 2, 1, NULL, NULL, NULL),
(640, NULL, 116, 'Labo Item (edit)', 3, 1, NULL, NULL, NULL),
(641, NULL, 116, 'Labo Item (delete)', 4, 1, NULL, NULL, NULL),
(642, NULL, 117, 'Labo Item Category (view)', 1, 1, NULL, NULL, NULL),
(643, NULL, 117, 'Labo Item Category (add)', 2, 1, NULL, NULL, NULL),
(644, NULL, 117, 'Labo Item Category (edit)', 3, 1, NULL, NULL, NULL),
(645, NULL, 117, 'Labo Item Category (delete)', 4, 1, NULL, NULL, NULL),
(663, NULL, 121, 'Labo Item Group (view)', 1, 1, NULL, NULL, NULL),
(664, NULL, 121, 'Labo Item Group (add)', 2, 1, NULL, NULL, NULL),
(665, NULL, 121, 'Labo Item Group (edit)', 3, 1, NULL, NULL, NULL),
(666, NULL, 121, 'Labo Item Group (delete)', 4, 1, NULL, NULL, NULL),
(667, NULL, 121, 'Labo Item Group (Set Price Insurance)', 5, 1, NULL, NULL, NULL),
(668, NULL, 121, 'Labo Item Group (Clone Service)', 6, 1, NULL, NULL, NULL),
(669, NULL, 121, 'Labo Item Group (Delete All Service)', 7, 1, NULL, NULL, NULL),
(670, NULL, 121, 'Labo Item Group (Export Excel)', 8, 1, NULL, NULL, NULL),
(671, NULL, 122, 'Labo Title Group (view)', 1, 1, NULL, NULL, NULL),
(672, NULL, 122, 'Labo Title Group (add)', 2, 1, NULL, NULL, NULL),
(673, NULL, 122, 'Labo Title Group (edit)', 3, 1, NULL, NULL, NULL),
(674, NULL, 122, 'Labo Title Group (delete)', 4, 1, NULL, NULL, NULL),
(675, NULL, 123, 'Labo Title Item (view)', 1, 1, NULL, NULL, NULL),
(676, NULL, 123, 'Labo Title Item (add)', 2, 1, NULL, NULL, NULL),
(677, NULL, 123, 'Labo Title Item (edit)', 3, 1, NULL, NULL, NULL),
(678, NULL, 123, 'Labo Title Item (delete)', 4, 1, NULL, NULL, NULL),
(679, NULL, 124, 'Labo Unit (view)', 1, 1, NULL, NULL, NULL),
(680, NULL, 124, 'Labo Unit (add)', 2, 1, NULL, NULL, NULL),
(681, NULL, 124, 'Labo Unit (edit)', 3, 1, NULL, NULL, NULL),
(682, NULL, 124, 'Labo Unit (delete)', 4, 1, NULL, NULL, NULL),
(683, NULL, 125, 'Labo Age (view)', 1, 1, NULL, NULL, NULL),
(684, NULL, 125, 'Labo Age (add)', 2, 1, NULL, NULL, NULL),
(685, NULL, 125, 'Labo Age (edit)', 3, 1, NULL, NULL, NULL),
(686, NULL, 125, 'Labo Age (delete)', 4, 1, NULL, NULL, NULL),
(687, NULL, 126, 'Labo Medicine (view)', 1, 1, NULL, NULL, NULL),
(688, NULL, 126, 'Labo Medicine (add)', 2, 1, NULL, NULL, NULL),
(689, NULL, 126, 'Labo Medicine (edit)', 3, 1, NULL, NULL, NULL),
(690, NULL, 126, 'Labo Medicine (delete)', 4, 1, NULL, NULL, NULL),
(691, NULL, 127, 'Labo Site (view)', 1, 1, NULL, NULL, NULL),
(692, NULL, 127, 'Labo Site (add)', 2, 1, NULL, NULL, NULL),
(693, NULL, 127, 'Labo Site (edit)', 3, 1, NULL, NULL, NULL),
(694, NULL, 127, 'Labo Site (delete)', 4, 1, NULL, NULL, NULL),
(695, NULL, 128, 'Labo Sub Title Group (View)', 1, 1, NULL, NULL, NULL),
(696, NULL, 128, 'Labo Sub Title Group (Add)', 2, 1, NULL, NULL, NULL),
(697, NULL, 128, 'Labo Sub Title Group (Edit)', 3, 1, NULL, NULL, NULL),
(698, NULL, 128, 'Labo Sub Title Group (Delete)', 4, 1, NULL, NULL, NULL),
(699, NULL, 128, 'Labo Sub Title Group (Export to Excel)', 5, 1, NULL, NULL, NULL),
(700, NULL, 113, 'Dashboard (Queue Labo Test)', 2, 1, NULL, NULL, NULL),
(701, NULL, 113, 'Dashboard (Void Service Invoice)', 2, 1, NULL, NULL, NULL),
(702, NULL, 113, 'Dashboard (Void Receipt)', 3, 1, NULL, NULL, NULL),
(703, NULL, 129, 'Patient History (View)', 1, 1, NULL, NULL, NULL),
(704, NULL, 130, 'Appointment (View)', 1, 1, NULL, NULL, NULL),
(705, NULL, 130, 'Appointment (Add)', 2, 1, NULL, NULL, NULL),
(706, NULL, 130, 'Appointment (Edit)', 3, 1, NULL, NULL, NULL),
(707, NULL, 130, 'Appointment (Delete)', 4, 1, NULL, NULL, NULL),
(708, NULL, 131, 'Company Insurance (View)', 1, 1, NULL, NULL, NULL),
(709, NULL, 131, 'Company Insurance (Add)', 2, 1, NULL, NULL, NULL),
(710, NULL, 131, 'Company Insurance (Edit)', 3, 1, NULL, NULL, NULL),
(711, NULL, 131, 'Company Insurance (Delete)', 4, 1, NULL, NULL, NULL),
(712, NULL, 132, 'Insurance Group (View)', 1, 1, NULL, NULL, NULL),
(713, NULL, 132, 'Insurance Group (Add)', 2, 1, NULL, NULL, NULL),
(714, NULL, 132, 'Insurance Group (Edit)', 3, 1, NULL, NULL, NULL),
(715, NULL, 132, 'Insurance Group (Delete)', 4, 1, NULL, NULL, NULL),
(716, NULL, 133, 'Insurance Service Price (View)', 1, 1, NULL, NULL, NULL),
(717, NULL, 133, 'Insurance Service Price (Add)', 2, 1, NULL, NULL, NULL),
(718, NULL, 133, 'Insurance Service Price (Edit)', 3, 1, NULL, NULL, NULL),
(719, NULL, 133, 'Insurance Service Price (Delete)', 4, 1, NULL, NULL, NULL),
(720, NULL, 133, 'Insurance Service Price (Clone Service)', 5, 1, NULL, NULL, NULL),
(721, NULL, 133, 'Insurance Service Price (Delete All Service)', 6, 1, NULL, NULL, NULL),
(722, NULL, 133, 'Insurance Service Price (Export to Excel)', 7, 1, NULL, NULL, NULL),
(723, NULL, 28, 'Report (Receipt)', 309, 1, NULL, NULL, NULL),
(724, NULL, 28, 'Report (Laboratory Service)', 904, 1, NULL, NULL, NULL),
(725, NULL, 28, 'Report (Section/Service)', 905, 1, NULL, NULL, NULL),
(726, NULL, 28, 'Report (Client/Insurance Provider)', 906, 1, NULL, NULL, NULL),
(727, NULL, 134, 'Follow Doctor', 1, 1, NULL, NULL, NULL),
(728, NULL, 134, 'Follow Nurse', 2, 1, NULL, NULL, NULL),
(729, NULL, 134, 'Follow Labo', 3, 1, NULL, NULL, NULL),
(730, NULL, 135, 'Dashboard (nurse)', 1, 1, NULL, NULL, NULL),
(731, NULL, 135, 'Nurse (Add Vital Sign)', 2, 1, NULL, NULL, NULL),
(732, NULL, 135, 'Nurse (Consultation)', 3, 1, NULL, NULL, NULL),
(733, NULL, 136, 'Echography Infomation (View)', 1, 1, NULL, NULL, NULL),
(734, NULL, 136, 'Echography Infomation (Add)', 2, 1, NULL, NULL, NULL),
(735, NULL, 136, 'Echography Infomation (Edit)', 3, 1, NULL, NULL, NULL),
(736, NULL, 136, 'Echography Infomation (Delete)', 4, 1, NULL, NULL, NULL),
(737, NULL, 137, 'Indication (View)', 1, 1, NULL, NULL, NULL),
(738, NULL, 137, 'Indication (Add)', 2, 1, NULL, NULL, NULL),
(739, NULL, 137, 'Indication (Edit)', 3, 1, NULL, NULL, NULL),
(740, NULL, 137, 'Indication (Delete)', 4, 1, NULL, NULL, NULL),
(741, NULL, 138, 'Echo Service (Dashboard)', 1, 1, NULL, NULL, NULL),
(742, NULL, 138, 'Echo Service (View)', 2, 1, NULL, NULL, NULL),
(743, NULL, 138, 'Echo Service (Edit)', 3, 1, NULL, NULL, NULL),
(744, NULL, 138, 'Echo Service (Print)', 4, 1, NULL, NULL, NULL),
(745, NULL, 138, 'Echo Service (Add From Recept)', 5, 1, NULL, NULL, NULL),
(746, NULL, 138, 'Echo Service (Add From Doctor)', 6, 1, NULL, NULL, NULL),
(747, NULL, 138, 'Echo Service (Add From Doctor Obstetnique)', 7, 1, NULL, NULL, NULL),
(748, NULL, 138, 'Echo Service (Add From Doctor Cardiaque)', 8, 1, NULL, NULL, NULL),
(749, NULL, 139, 'Echo Service Cardia (View)', 1, 1, NULL, NULL, NULL),
(750, NULL, 139, 'Echo Service Cardia (Edit)', 2, 1, NULL, NULL, NULL),
(751, NULL, 139, 'Echo Service Cardia (Print)', 3, 1, NULL, NULL, NULL),
(752, NULL, 140, 'Echographie Patient Obstetnique (View)', 1, 1, NULL, NULL, NULL),
(753, NULL, 140, 'Echographie Patient Obstetnique (Edit)', 2, 1, NULL, NULL, NULL),
(754, NULL, 140, 'Echographie Patient Obstetnique (Print)', 3, 1, NULL, NULL, NULL),
(755, NULL, 141, 'X-Ray Service (Dashboard)', 1, 1, NULL, NULL, NULL),
(756, NULL, 141, 'X-Ray Service (View)', 2, 1, NULL, NULL, NULL),
(757, NULL, 141, 'X-Ray Service (Edit)', 3, 1, NULL, NULL, NULL),
(758, NULL, 141, 'X-Ray Service (Print)', 4, 1, NULL, NULL, NULL),
(759, NULL, 141, 'X-Ray Service (Add From Recept)', 5, 1, NULL, NULL, NULL),
(760, NULL, 141, 'X-Ray Service (Add From Doctor)', 6, 1, NULL, NULL, NULL),
(761, NULL, 142, 'Mid Wife Service (Dashboard)', 1, 1, NULL, NULL, NULL),
(762, NULL, 142, 'Mid Wife Service (View)', 2, 1, NULL, NULL, NULL),
(763, NULL, 142, 'Mid Wife Service (Edit)', 3, 1, NULL, NULL, NULL),
(764, NULL, 142, 'Mid Wife Service (Print)', 4, 1, NULL, NULL, NULL),
(765, NULL, 142, 'Mid Wife Service (Add From Recept)', 5, 1, NULL, NULL, NULL),
(766, NULL, 142, 'Mid Wife Service (Add From Doctor)', 6, 1, NULL, NULL, NULL),
(767, NULL, 142, 'Mid Wife Service (Add Check Up Patient)', 7, 1, NULL, NULL, NULL),
(768, NULL, 142, 'Mid Wife Service (Edit Check Up Patient)', 8, 1, NULL, NULL, NULL),
(769, NULL, 142, 'Mid Wife Service (Add and Edit Mid Wife Service)', 9, 1, NULL, NULL, NULL),
(770, NULL, 142, 'Mid Wife Service (Add New Mid Wife Service)', 10, 1, NULL, NULL, NULL),
(771, NULL, 142, 'Mid Wife Service (Add Mid Wife Service (Dossier Medical))', 11, 1, NULL, NULL, NULL),
(772, NULL, 142, 'Mid Wife Service (Add New Dossier Medical)', 12, 1, NULL, NULL, NULL),
(773, NULL, 142, 'Mid Wife Service (Edit Dossier Medical)', 13, 1, NULL, NULL, NULL),
(774, NULL, 142, 'Mid Wife Service (Add New Tracking)', 14, 1, NULL, NULL, NULL),
(775, NULL, 142, 'Mid Wife Service (Edit Tracking)', 15, 1, NULL, NULL, NULL),
(776, NULL, 142, 'Mid Wife Service (Add New Accouchement)', 16, 1, NULL, NULL, NULL),
(777, NULL, 142, 'Mid Wife Service (Edit Accouchement)', 17, 1, NULL, NULL, NULL),
(778, NULL, 142, 'Mid Wife Service (Add New Deliverance)', 18, 1, NULL, NULL, NULL),
(779, NULL, 142, 'Mid Wife Service (Edit Deliverance)', 19, 1, NULL, NULL, NULL),
(780, NULL, 142, 'Mid Wife Service (Add New Accouchement First Time)', 20, 1, NULL, NULL, NULL),
(781, NULL, 142, 'Mid Wife Service (Edit Accouchement First Time)', 21, 1, NULL, NULL, NULL),
(782, NULL, 142, 'Mid Wife Service (Add New Accouchement Next Time)', 22, 1, NULL, NULL, NULL),
(783, NULL, 142, 'Mid Wife Service (Edit Accouchement Next Time)', 23, 1, NULL, NULL, NULL),
(784, NULL, 143, 'Doctor Consultation (View)', 1, 1, NULL, NULL, NULL),
(785, NULL, 143, 'Doctor Consultation (Add)', 2, 1, NULL, NULL, NULL),
(786, NULL, 143, 'Doctor Consultation (Edit)', 3, 1, NULL, NULL, NULL),
(787, NULL, 143, 'Doctor Consultation (Delete)', 4, 1, NULL, NULL, NULL),
(788, NULL, 130, 'Dashboard (Appointment)', 1, 1, NULL, NULL, NULL),
(789, NULL, 144, 'Chief Complain (View)', 1, 1, NULL, NULL, NULL),
(790, NULL, 144, 'Chief Complain (Add)', 2, 1, NULL, NULL, NULL),
(791, NULL, 144, 'Chief Complain (Edit)', 3, 1, NULL, NULL, NULL),
(792, NULL, 144, 'Chief Complain (Delete)', 4, 1, NULL, NULL, NULL),
(793, NULL, 145, 'Diagnostic (View)', 1, 1, NULL, NULL, NULL),
(794, NULL, 145, 'Diagnostic (Add)', 2, 1, NULL, NULL, NULL),
(795, NULL, 145, 'Diagnostic (Edit)', 3, 1, NULL, NULL, NULL),
(796, NULL, 145, 'Diagnostic (Delete)', 4, 1, NULL, NULL, NULL),
(797, NULL, 146, 'Examination (View)', 1, 1, NULL, NULL, NULL),
(798, NULL, 146, 'Examination (Add)', 2, 1, NULL, NULL, NULL),
(799, NULL, 146, 'Examination (Edit)', 3, 1, NULL, NULL, NULL),
(800, NULL, 146, 'Examination (Delete)', 4, 1, NULL, NULL, NULL),
(801, NULL, 147, 'Cystoscopy Service (Dashboard)', 1, 1, NULL, NULL, NULL),
(802, NULL, 147, 'Cystoscopy Service (View)', 2, 1, NULL, NULL, NULL),
(803, NULL, 147, 'Cystoscopy Service (Edit)', 3, 1, NULL, NULL, NULL),
(804, NULL, 147, 'Cystoscopy Service (Add Form Doctor)', 4, 1, NULL, NULL, NULL),
(805, NULL, 147, 'Cystoscopy Service (Print)', 5, 1, NULL, NULL, NULL),
(806, NULL, 148, 'Uroflowmetry Service (Dashboard)', 1, 1, NULL, NULL, NULL),
(807, NULL, 148, 'Uroflowmetry Service (View)', 2, 1, NULL, NULL, NULL),
(808, NULL, 148, 'Uroflowmetry Service (Edit)', 3, 1, NULL, NULL, NULL),
(809, NULL, 148, 'Uroflowmetry Service (Add Form Doctor)', 4, 1, NULL, NULL, NULL),
(810, NULL, 148, 'Uroflowmetry Service (Print)', 5, 1, NULL, NULL, NULL),
(811, NULL, 149, 'Patient IPD (view-admission)', 1, 1, NULL, NULL, NULL),
(812, NULL, 149, 'Patient IPD (add-admission)', 2, 1, NULL, NULL, NULL),
(813, NULL, 149, 'Patient IPD (edit-admission)', 3, 1, NULL, NULL, NULL),
(814, NULL, 149, 'Patient IPD (delete-admission)', 4, 1, NULL, NULL, NULL),
(815, NULL, 149, 'Patient IPD (view-medical-surgery)', 5, 1, NULL, NULL, NULL),
(816, NULL, 149, 'Patient IPD (add-medical-surgery)', 6, 1, NULL, NULL, NULL),
(817, NULL, 149, 'Patient IPD (edit-medical-surgery)', 7, 1, NULL, NULL, NULL),
(818, NULL, 149, 'Patient IPD (delete-medical-surgery)', 8, 1, NULL, NULL, NULL),
(819, NULL, 149, 'Patient IPD (addService-admission)', 5, 1, NULL, NULL, NULL),
(820, NULL, 149, 'Patient IPD (addService-medical-surgery)', 9, 1, NULL, NULL, NULL),
(821, NULL, 150, 'Patient Emergency (view)', 1, 1, NULL, NULL, NULL),
(822, NULL, 150, 'Patient Emergency (add)', 2, 1, NULL, NULL, NULL),
(823, NULL, 150, 'Patient Emergency (edit)', 3, 1, NULL, NULL, NULL),
(824, NULL, 150, 'Patient Emergency (delete)', 4, 1, NULL, NULL, NULL),
(825, NULL, 150, 'Patient Emergency (view detail)', 5, 1, NULL, NULL, NULL),
(826, NULL, 151, 'Patient IPD Certificate (view)', 1, 1, NULL, NULL, NULL),
(827, NULL, 151, 'Patient IPD Certificate (add)', 2, 1, NULL, NULL, NULL),
(828, NULL, 151, 'Patient IPD Certificate (edit)', 3, 1, NULL, NULL, NULL),
(829, NULL, 151, 'Patient IPD Certificate (delete)', 4, 1, NULL, NULL, NULL),
(830, NULL, 152, 'Room (view)', 1, 1, NULL, NULL, NULL),
(831, NULL, 152, 'Room (add)', 2, 1, NULL, NULL, NULL),
(832, NULL, 152, 'Room (edit)', 3, 1, NULL, NULL, NULL),
(833, NULL, 152, 'Room (delete)', 4, 1, NULL, NULL, NULL),
(834, NULL, 115, 'Labo (Approve)', 2, 1, NULL, NULL, NULL),
(835, NULL, 115, 'Labo (Disapprove)', 3, 1, NULL, NULL, NULL),
(836, NULL, 153, 'Medical History (View)', 1, 1, NULL, NULL, NULL),
(837, NULL, 153, 'Medical History (Add)', 2, 1, NULL, NULL, NULL),
(838, NULL, 153, 'Medical History (Edit)', 3, 1, NULL, NULL, NULL),
(839, NULL, 153, 'Medical History (Delete)', 4, 1, NULL, NULL, NULL),
(840, NULL, 154, 'Doctor Comments (View)', 1, 1, NULL, NULL, NULL),
(841, NULL, 154, 'Doctor Comments (Add)', 2, 1, NULL, NULL, NULL),
(842, NULL, 154, 'Doctor Comments (Edit)', 3, 1, NULL, NULL, NULL),
(843, NULL, 154, 'Doctor Comments (Delete)', 4, 1, NULL, NULL, NULL),
(844, NULL, 155, 'Frequency (View)', 1, 1, NULL, NULL, NULL),
(845, NULL, 155, 'Frequency (Add)', 2, 1, NULL, NULL, NULL),
(846, NULL, 155, 'Frequency (Edit)', 3, 1, NULL, NULL, NULL),
(847, NULL, 155, 'Frequency (Delete)', 4, 1, NULL, NULL, NULL),
(848, NULL, 156, 'Daily Clinical Report (View)', 1, 1, NULL, NULL, NULL),
(849, NULL, 156, 'Daily Clinical Report (Add)', 2, 1, NULL, NULL, NULL),
(850, NULL, 156, 'Daily Clinical Report (Edit)', 3, 1, NULL, NULL, NULL),
(851, NULL, 156, 'Daily Clinical Report (Delete)', 4, 1, NULL, NULL, NULL),
(852, 'abc9f4ce2668245404b5d76ed0dd4081', 28, 'Report (Case Expense)', 1, 1, NULL, NULL, NULL),
(853, NULL, 28, 'Report (Convert)', 1, 1, NULL, NULL, NULL),
(854, NULL, 157, 'Referrals (Add)', 1, 1, NULL, NULL, NULL),
(855, NULL, 157, 'Referrals (Edit)', 2, 1, NULL, NULL, NULL),
(856, NULL, 157, 'Referrals (View)', 3, 1, NULL, NULL, NULL),
(857, NULL, 157, 'Referrals (Print)', 4, 1, NULL, NULL, NULL),
(858, NULL, 157, 'Referrals (Delete)', 5, 1, NULL, NULL, NULL),
(859, NULL, 157, 'Referrals (Export Excel)', 6, 1, NULL, NULL, NULL),
(861, NULL, 28, 'Report (Referral)', 907, 1, NULL, NULL, NULL),
(862, NULL, 21, 'Purchase Bill (Show Unit Cost)', 11, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `module_code_branches`
--

CREATE TABLE IF NOT EXISTS `module_code_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `adj_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tr_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pos_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pos_rep_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quote_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `so_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inv_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inv_rep_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dn_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_pay_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cm_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cm_rep_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `po_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pb_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pb_rep_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `br_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `br_rep_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pay_bill_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cus_consign_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cus_consign_return_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ven_consign_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ven_consign_return_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `landed_cost_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `landed_cost_receipt_code` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_collect_shift` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_id` (`branch_id`),
  KEY `company_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36 ;

--
-- Dumping data for table `module_code_branches`
--

INSERT INTO `module_code_branches` (`id`, `branch_id`, `adj_code`, `request_code`, `to_code`, `tr_code`, `pos_code`, `pos_rep_code`, `quote_code`, `so_code`, `inv_code`, `inv_rep_code`, `dn_code`, `receive_pay_code`, `cm_code`, `cm_rep_code`, `po_code`, `pb_code`, `pb_rep_code`, `br_code`, `br_rep_code`, `pay_bill_code`, `cus_consign_code`, `cus_consign_return_code`, `ven_consign_code`, `ven_consign_return_code`, `landed_cost_code`, `landed_cost_receipt_code`, `receive_collect_shift`) VALUES
(35, 1, 'ADJ', NULL, 'TO', NULL, 'POS', 'POR', NULL, 'SO', 'INV', 'INVR', 'DN', 'RPC', 'CM', 'CRC', NULL, 'PB', 'PRC', 'BR', 'BRR', 'PAC', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `module_code_branches`
--
DROP TRIGGER IF EXISTS `zModuleCodeBranchBfInsert`;
DELIMITER //
CREATE TRIGGER `zModuleCodeBranchBfInsert` BEFORE INSERT ON `module_code_branches`
 FOR EACH ROW BEGIN
	IF NEW.branch_id = "" OR NEW.branch_id = NULL OR NEW.adj_code = "" OR NEW.adj_code = NULL OR NEW.request_code = "" OR NEW.request_code = NULL OR NEW.to_code = "" OR NEW.to_code = NULL OR NEW.tr_code = "" OR NEW.tr_code = NULL OR NEW.pos_code = "" OR NEW.pos_code = NULL OR NEW.pos_rep_code = "" OR NEW.pos_rep_code = NULL OR NEW.quote_code = "" OR NEW.quote_code = NULL OR NEW.so_code = "" OR NEW.so_code = NULL OR NEW.inv_code = "" OR NEW.inv_code = NULL OR NEW.inv_rep_code = "" OR NEW.inv_rep_code = NULL OR NEW.dn_code = "" OR NEW.dn_code = NULL OR NEW.receive_pay_code = "" OR NEW.receive_pay_code = NULL OR NEW.cm_code = "" OR NEW.cm_code = NULL OR NEW.cm_rep_code = "" OR NEW.cm_rep_code = NULL OR NEW.po_code = "" OR NEW.po_code = NULL OR NEW.pb_code = "" OR NEW.pb_code = NULL OR NEW.pb_rep_code = "" OR NEW.pb_rep_code = NULL OR NEW.br_code = "" OR NEW.br_code = NULL OR NEW.br_rep_code = "" OR NEW.br_rep_code = NULL OR NEW.pay_bill_code = "" OR NEW.pay_bill_code = NULL OR NEW.cus_consign_code = "" OR NEW.cus_consign_code = NULL OR NEW.cus_consign_return_code = "" OR NEW.cus_consign_return_code = NULL OR NEW.ven_consign_code = "" OR NEW.ven_consign_code = NULL OR NEW.ven_consign_return_code = "" OR NEW.ven_consign_return_code = NULL OR NEW.landed_cost_code = "" OR NEW.landed_cost_code = NULL OR NEW.landed_cost_receipt_code = "" OR NEW.landed_cost_receipt_code = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `module_details`
--

CREATE TABLE IF NOT EXISTS `module_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `controllers` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `views` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `controllers` (`controllers`),
  KEY `views` (`views`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2329 ;

--
-- Dumping data for table `module_details`
--

INSERT INTO `module_details` (`id`, `sys_code`, `module_id`, `controllers`, `views`) VALUES
(1, '84917b27b1615c1eaa88d5eef015f895', 1, 'dashboards', 'index'),
(2, '0efc1f8b66bb82075a1f2a168e2203ec', 2, 'users', 'index'),
(3, '24d9a285aed92cab4cee68d12fa89d27', 2, 'users', 'ajax'),
(4, '2a485bff50b2c8f3b17d22c9ebd6dc8d', 2, 'users', 'view'),
(8, 'ad107e76132bd43a6eef24c8f75579ab', 6, 'groups', 'index'),
(9, '75d596ae8e82553aa63bd1db85318930', 6, 'groups', 'ajax'),
(10, '2b060322a933ed555a0cdf275b61d204', 6, 'groups', 'view'),
(14, 'fb0dfc9d92c68a3da58b5f48779ecbf5', 10, 'locations', 'index'),
(15, 'd78f9ce4ec8acdfc64be4077934fc67b', 10, 'locations', 'ajax'),
(16, 'd45bcecb54ae1a92391e7daec71292ed', 10, 'locations', 'view'),
(17, 'b5735ab1ef1439280c9ca2fa13fc97b1', 11, 'locations', 'add'),
(18, 'fa31812bfec1250421d882f4146bf10f', 12, 'locations', 'edit'),
(19, '3f329b93d7fee1e640700cd71c7218e9', 13, 'locations', 'delete'),
(20, '51f450716c389c9d22c69e3b23998811', 14, 'location_groups', 'index'),
(21, '07727c1bb0aac27ba7fdd09685839f65', 14, 'location_groups', 'ajax'),
(22, 'e6f3b419cc4a01f5ce5cab00e7a7e3a0', 14, 'location_groups', 'view'),
(23, 'edf8c8f04f5fb0b955d52f120bde634c', 15, 'location_groups', 'add'),
(24, '1ae974cc70d6f9ba7248c65858022f2c', 16, 'location_groups', 'edit'),
(25, 'ecafc244d1af8c56adfdad541aaf7633', 17, 'location_groups', 'delete'),
(26, '0fb043d270a4feda8dc8ade39c3382f0', 18, 'uoms', 'index'),
(27, '1de381425e2e643f68299c1277739bba', 18, 'uoms', 'ajax'),
(28, '125cb0786342394b172930c2f91e8119', 18, 'uoms', 'view'),
(29, 'b16bc6ca55b7e75e89dd6c90f9d06ed6', 19, 'uoms', 'add'),
(30, '30434ce13d3074a99ac12a15e7826904', 20, 'uoms', 'edit'),
(31, '4119adbc68481e015c2746af84736198', 21, 'uoms', 'delete'),
(32, 'ef925b11ddf6a56bc58f2643ba7164a8', 22, 'uom_conversions', 'index'),
(33, '91bc407e4894a5bedb037ecaf651cf8a', 22, 'uom_conversions', 'ajax'),
(34, '2446a2c0f6cbbe5f0243cea095d8326b', 22, 'uom_conversions', 'view'),
(35, 'fc1bcf8676b01d8d57e4ef021d8e0610', 23, 'uom_conversions', 'add'),
(36, 'a443292c5903e42d0d5750830161d7ea', 24, 'uom_conversions', 'edit'),
(37, 'c432c29e22a59097c568a4c65e16af37', 25, 'uom_conversions', 'delete'),
(38, 'fbcc9cd5832ac451ef135d36b1356f15', 26, 'provinces', 'index'),
(39, 'ed6e800e232b798be56138481ed22865', 26, 'provinces', 'ajax'),
(40, '0d53b41d14a3cdeee7ecb24af4cb9ec2', 26, 'provinces', 'view'),
(41, 'bcfafd15de2d252e49698e7887ca6455', 27, 'provinces', 'add'),
(42, '707d406613aa10f5cfa76a653757b8e9', 28, 'provinces', 'edit'),
(43, '3ca592197111aeb40c8ab4a996ceb85b', 29, 'provinces', 'delete'),
(44, 'd157f4040960df78dc6846eba5e7d510', 30, 'districts', 'index'),
(45, 'fb9e58b28351f97c12b4e68eeacdb9a0', 30, 'districts', 'ajax'),
(46, '487e8f9c8b51856995275c7d1eb3f911', 30, 'districts', 'view'),
(47, '8b8f8a4c34166f24be42ea237b8c9d43', 31, 'districts', 'add'),
(48, '62042da7c11a2577c75b0da12ebbf2be', 32, 'districts', 'edit'),
(49, 'f93ccca7596a9f1dcb080c349bdef63e', 33, 'districts', 'delete'),
(50, '48e692146776765c4deb84855c097825', 34, 'communes', 'index'),
(51, 'd744106c66b4b3a9bda7edc594f5407a', 34, 'communes', 'ajax'),
(52, '0ba9d573ebe0ef7ef8fcc15ec9993572', 34, 'communes', 'view'),
(53, '588cf906a2a30703d70491479a2954d7', 35, 'communes', 'add'),
(54, '820ecbf9aae7e0fa7e2c76a8c107b925', 36, 'communes', 'edit'),
(55, '0729f148c6141d1336564ec8a966452f', 37, 'communes', 'delete'),
(56, '3f34f6f526e8879529237b780e6670ce', 38, 'villages', 'index'),
(57, '78b6ed34d83f98e13fcba291aa9f0ba8', 38, 'villages', 'ajax'),
(58, '3622d7176bbba5c20ba92a4394cf4ba6', 38, 'villages', 'view'),
(59, '954f5e1a8b186b1aa68650fe0109b025', 39, 'villages', 'add'),
(60, 'ceab6577ca37fe64824353aca5243d53', 40, 'villages', 'edit'),
(61, '9bdafce98763760cd7b5a9c1c9fa97b1', 41, 'villages', 'delete'),
(62, '5e323edf6f564149127c26ceb6da4002', 42, 'cgroups', 'index'),
(63, 'acb3890fbb111e0c5cb1a73877ceced9', 42, 'cgroups', 'ajax'),
(64, '96ec2139098d9acf8ada0bdc70e5cc39', 42, 'cgroups', 'view'),
(65, '0eb7f42eb7186e0f8fc7119d89134f71', 43, 'cgroups', 'add'),
(66, 'f6befbd47235b494f2fd0d45c3c6b988', 44, 'cgroups', 'edit'),
(67, '7f3853fb73085cf89c58e08b25747171', 45, 'cgroups', 'delete'),
(68, 'f2c0db37748062d2c05f72290d65e2a1', 46, 'products', 'index'),
(69, 'c0fa121fd4b6a78a3dc6b74b47efd019', 46, 'products', 'ajax'),
(70, '3e940da3a1319dd1323493956aad8cbe', 46, 'products', 'view'),
(71, 'dfed238adcea6ae47b348e539c96da50', 47, 'products', 'add'),
(72, 'a1dec6487100d8d30aaccc173c05e74c', 48, 'products', 'edit'),
(73, 'e406bd6f4922d584f0d94e2f3cf9cd48', 49, 'products', 'delete'),
(74, '53416113ffa4ef61ed1d4e4c677f7ab4', 50, 'customers', 'index'),
(75, '929a2792c9b65f4c44bd0a05cc2fccdf', 50, 'customers', 'ajax'),
(76, '8fa21caa93d0c1c441679af8046ea240', 50, 'customers', 'view'),
(77, '59497cdf35bc0b2730cc020c2ea5f7ac', 51, 'customers', 'add'),
(78, '21af95a7a72296de3693cb45bb38187f', 52, 'customers', 'edit'),
(79, 'b67d23c6ba2d0ea6042195fc4001796a', 53, 'customers', 'delete'),
(98, '2f9bc51b8d2e133cb291d53c150484cb', 60, 'pgroups', 'index'),
(99, '3d4d089a5749c4e680d6100c79a6d9d8', 60, 'pgroups', 'ajax'),
(100, 'a6e3be560bf34e0eebf29476e1e6f7a2', 60, 'pgroups', 'view'),
(101, '17297160829530a4eaa3e59cbf326cee', 61, 'pgroups', 'add'),
(102, '9978eb6098d1dd3bd8ebcd2a124a122c', 62, 'pgroups', 'edit'),
(103, 'bfd7efa256e03c46841caacd6d2efbb5', 63, 'pgroups', 'delete'),
(104, '9b55d6cf1e77df7794a3923b8f7c9c80', 60, 'pgroups', 'searchProduct'),
(116, '0449fcde7c32083400aa6f1a005cfba0', 72, 'vendors', 'index'),
(117, '16cb425addc0df86f6c0ece4ff3b5c72', 72, 'vendors', 'ajax'),
(118, '40bd3610341d2cccbf14461982523990', 73, 'vendors', 'add'),
(119, 'cd5b9aaf943649d27dc8a3822f52a99f', 74, 'vendors', 'edit'),
(120, 'c04e90a3277d50cf59a4e7d16cddd021', 75, 'vendors', 'delete'),
(123, '2dc224ac2909ae4794ae74d549d8d95c', 76, 'chart_accounts', 'index'),
(124, '28d405b667f1d815f6e8f9db54124de0', 76, 'chart_accounts', 'ajax'),
(125, '7cf912db0289f167e4d6db163c3783f6', 76, 'chart_accounts', 'view'),
(126, '81fd93f0fc60b00d82ab90a7750f3efa', 77, 'chart_accounts', 'add'),
(127, '0ff58820e40b12795ee4c0e3aaa18b21', 78, 'chart_accounts', 'edit'),
(128, '6fd1838b1dd5b6a5847eaee935d01fde', 79, 'chart_accounts', 'status'),
(129, '3517df498db33fc58e476de3a16ce11f', 80, 'chart_account_types', 'index'),
(130, 'a2d73cd07e79325b21f63f5a34361ebb', 80, 'chart_account_types', 'ajax'),
(134, '821362b57383de8a870bd417b24e593d', 83, 'chart_account_types', 'status'),
(135, '69fbfe3d0e6b02ce65e689abe10f5ae4', 84, 'chart_account_groups', 'index'),
(136, 'ebcce28ac8e912b0c2bc366e75f790e0', 84, 'chart_account_groups', 'ajax'),
(137, '91c1f0796042b84e309cb0cf286ea0d3', 84, 'chart_account_groups', 'view'),
(138, 'e07168b52a9af929aedc08abb8d5870f', 85, 'chart_account_groups', 'add'),
(139, '7563cda05f3bcc44c21737174dba4bd1', 86, 'chart_account_groups', 'edit'),
(140, 'f3e85c81e86b32ecc7236dd0f2f73658', 87, 'chart_account_groups', 'status'),
(144, '0c80e140cce071b2934e73b1f2af08b4', 91, 'purchase_orders', 'index'),
(145, '8b05c16e5e3dd0c62973fd58286f71b2', 91, 'purchase_orders', 'ajax'),
(146, '0dbf0cadc01f86d2d515f768115efc7f', 91, 'purchase_orders', 'view'),
(147, '88711465ac01eacb1e0dfa9d082b43e5', 92, 'purchase_orders', 'add'),
(149, 'b5405336ea8c2cdd8eeef2b023f2c341', 94, 'purchase_orders', 'delete'),
(151, '4c016c921034b0baf431d57e1c57d9d4', 46, 'products', 'searchProduct'),
(154, '803278545f2f71c7b5383baeb5229716', 72, 'vendors', 'view'),
(158, '9beabcc973147bf52b22ab95cdbea689', 96, 'purchase_receives', 'index'),
(159, '23ff3328001aac8d359447d446a0043a', 96, 'purchase_receives', 'ajax'),
(160, '0905d69fdf0ba04856c6b55edf390570', 96, 'purchase_receives', 'view'),
(164, 'a6961171ec65fd7e0b7c08d8367a0280', 92, 'uoms', 'getRelativeUom'),
(166, '7d0e63757d701a6b4f69543079e4de5a', 101, 'transfer_orders', 'index'),
(167, '5054108ee76c091085701f8779580134', 101, 'transfer_orders', 'ajax'),
(168, '73b7d5b8ea0db6652471fc583d06812d', 101, 'transfer_orders', 'view'),
(169, '67a3e21a8d8b868215c35ed068c1ed92', 102, 'transfer_orders', 'add'),
(170, '274e8eb86f14118183be4f161296e8fa', 103, 'transfer_orders', 'edit'),
(171, '7075ca4f69488200111c841533f0704d', 104, 'transfer_orders', 'delete'),
(172, '54cd53061ffaa8d4fc9d9bd5a1172206', 105, 'sales_orders', 'index'),
(173, '19df4ecd458f5d087f7cb6797daac326', 105, 'sales_orders', 'ajax'),
(174, '1564f33858a783271d50eb22af3018b6', 105, 'sales_orders', 'view'),
(184, '5c1efe079199a0e3829ae3d1b7e300b0', 97, 'purchase_receives', 'receive'),
(185, '75fc65a9ff127be5d98f07d2fb7b1c0c', 109, 'exchange_rates', 'index'),
(186, '99201953f4dce741389069c960a07741', 109, 'exchange_rates', 'ajax'),
(188, '2f26d0c19f890d21a10e92ebe0c513bf', 110, 'exchange_rates', 'add'),
(193, '8f9ae51c3b0b922661db4d9735c5bb33', 46, 'products', 'searchProductByCode'),
(195, 'c10cae2024c17b1286e30bf554b11c89', 97, 'purchase_receives', 'receiveAll'),
(196, 'd5055de6d065044d45f5012ccbc197df', 113, 'general_ledgers', 'index'),
(197, '4a8e7ba7933f439ba15c4d646f018954', 113, 'general_ledgers', 'ajax'),
(198, '7a358ebf93e32d7f6d644a8945160460', 113, 'general_ledgers', 'view'),
(199, 'a63cee420b24b9bc0e0472835638ab77', 114, 'general_ledgers', 'add'),
(200, '324fdd06ee1112ad20277fc0fdf93419', 115, 'general_ledgers', 'edit'),
(201, '08a60a5787695383548117165c94cbdd', 116, 'general_ledgers', 'delete'),
(203, '5e2689364dd100cd4e88b65ac08e859a', 118, 'reports', 'trialBalance'),
(204, 'be4575682295586e60771c4bfb675dc5', 118, 'reports', 'trialBalanceResult'),
(205, '70bd1e4fede8ff7fd50e044799a2e542', 106, 'uoms', 'getRelativeUom'),
(207, '264689a4cfe6a9c5899e4faab983e7de', 119, 'reports', 'profitLoss'),
(208, 'b4583f581ae5c964b327314b8d421369', 119, 'reports', 'profitLossResult'),
(209, '65c26be580430d8c48f171bb79ec1704', 121, 'discounts', 'index'),
(210, 'e375b218f1f98ca89353efecb52e2eea', 121, 'discounts', 'ajax'),
(211, '81740467058c91a632fba5057cd23b4d', 121, 'discounts', 'view'),
(212, '5c1eef67cc5a8618a00be4774915da90', 122, 'discounts', 'add'),
(213, 'f95e047a05eedac497c15790d6994a04', 123, 'discounts', 'edit'),
(214, '36583266bf31fa94b7f753046ae751a5', 124, 'discounts', 'delete'),
(215, '22232684cb23474b2a6297f4a48218f7', 120, 'reports', 'balanceSheet'),
(216, '71dedfcf7444a854c5c5df308a46fc73', 120, 'reports', 'balanceSheetResult'),
(218, 'eba5b21b2b01c1d0cf6aa94b396f7137', 102, 'uoms', 'getRelativeUom'),
(219, 'c6ed55b38f9ad5c50e4969bbce027b31', 103, 'uoms', 'getRelativeUom'),
(222, '9b8f17d171bf25873aa1db230acdc14f', 125, 'transfer_receives', 'index'),
(223, 'f642bfa9916371e06c774786c1f835c8', 125, 'transfer_receives', 'ajax'),
(224, '39e04145c886bd78b486d182c8d06651', 125, 'transfer_receives', 'receive'),
(225, '83a4bf9118b2e659b09b496b07791fd0', 126, 'transfer_receives', 'receiveDetail'),
(227, '5ff1da82189cc6b4e88a3e94224179c6', 128, 'companies', 'index'),
(228, '2a9d5b66aae44027caceba629bcbc971', 128, 'companies', 'ajax'),
(229, 'b6f536b28af19adf69fbf82827bc7d7d', 128, 'companies', 'view'),
(230, '8d58f473d0f74b209e8cc5afabba02f6', 129, 'companies', 'add'),
(231, 'a68ab7bb3c51360f8df2119da947033c', 130, 'companies', 'edit'),
(232, 'eb37f264993e719b985b0827716e483e', 131, 'companies', 'delete'),
(235, '5f183c7202f4c2e5dcc0c3a264b80308', 126, 'transfer_receives', 'action'),
(238, 'c95d733d89be87d9019f33af34cfdb4c', 118, 'reports', 'trialBalanceResultByMonth'),
(239, 'b5a8af2b9bc0bee779685433918ad783', 119, 'reports', 'profitLossResultByMonth'),
(240, 'e37728800135383ff6bc89bf2de68830', 120, 'reports', 'balanceSheetResultByMonth'),
(241, '755ecb907b6e03a36c99818f404cd513', 126, 'transfer_receives', 'void'),
(245, '375b17cc6ae5829dab04862fa16b5050', 484, 'users', 'editProfile'),
(248, '3ba9ace7c09fa0f517fca796344311e3', 132, 'reports', 'customerAddressList'),
(249, 'e64ced861bf850e6d309fe54bcaf188d', 132, 'reports', 'customerAddressListResult'),
(250, '6f222e02ee7e300d2a8cdec3dc0ca214', 114, 'general_ledgers', 'checkCompany'),
(251, 'a74df0c20428e96a9c7e880e45391264', 115, 'general_ledgers', 'checkCompany'),
(253, '02f376bba3cc06e408138dbcbcef269a', 134, 'reports', 'productAging'),
(254, '37b42de13d583f488b734cfddb431fc5', 134, 'reports', 'productAgingResult'),
(255, '9eae43c98235add1cc6975e8365f8e5c', 133, 'reports', 'customerAddressDetail'),
(256, '4681847dfd4162a02c3499418c8eacbe', 133, 'reports', 'customerAddressDetailResult'),
(257, 'dcd2a7090d3fab871b994489d50a23cf', 135, 'reports', 'cashFlow'),
(258, '4fe19f935b709bd92bb8c4018fd9b957', 135, 'reports', 'cashFlowResult'),
(259, '5626635e09fcf775f0d473046ffbcebe', 135, 'reports', 'cashFlowResultByMonth'),
(260, '7ea578d1347739666fe631c92b1734bd', 43, 'cgroups', 'customer'),
(261, '9d275442f81d2384f00bad9918004a4c', 43, 'cgroups', 'customer_ajax'),
(262, 'b4145eb4074ce7b1f97326c903faa17f', 44, 'cgroups', 'customer_ajax'),
(263, '1f9309779306a5f901efe236d9fa7534', 44, 'cgroups', 'customer'),
(264, 'd9cba1cd933138d96e52627e1a7341bd', 398, 'purchase_orders', 'discount'),
(267, '92333c2e319287702f726c49d3d77ae9', 60, 'pgroups', 'product'),
(268, '875cc80e11c565f9243ebe72fff6050a', 60, 'pgroups', 'productAjax'),
(269, '5c5fa2374f436c09e294cbe1e3179cab', 127, 'uoms', 'getRelativeUomByProductId'),
(273, 'a30b5a35210748360c93c5208988b5c3', 137, 'sections', 'view'),
(274, 'f25c73826847ba5abafaa296e5720f53', 137, 'sections', 'index'),
(275, 'a1772e9fe5ee12bb974ab2a36fea29bd', 137, 'sections', 'ajax'),
(276, '4476ff6f6004718973d4f92f19cdce9b', 138, 'sections', 'add'),
(277, '462ad029ec3a7b5e871700fc91c95611', 139, 'sections', 'edit'),
(278, '1b2fad61139c19ad3ff57b82c78d3870', 140, 'sections', 'delete'),
(279, '9157846eff56241aeb381028c4a821b9', 141, 'services', 'view'),
(280, 'faa73da957e80e3b8e8a2d385d84f262', 141, 'services', 'index'),
(281, 'e69126f5d82f62d40aaef1e2854f1b76', 141, 'services', 'ajax'),
(282, '5345f8e499e8527828f57c8491911b71', 142, 'services', 'add'),
(283, '022db11bfd1264ffe492c271542caa72', 143, 'services', 'edit'),
(284, '797863b1dc71c11d02d8151ececd9a38', 144, 'services', 'delete'),
(286, 'dadc6ea65107233623e147b2ec7374e3', 118, 'reports', 'trialBalanceResultPeriod'),
(287, 'b706dfd3de61a6997823d7b4e93661f9', 118, 'reports', 'trialBalanceResultPeriodByMonth'),
(288, '63d78d4e6934ac2f61ffb810686913f8', 145, 'purchase_orders', 'aging'),
(289, '75d2ac4e5763beada20e6ce4a9cb3762', 91, 'purchase_orders', 'printInvoice'),
(290, 'e87eebb8408339e20a6ef4325d4bd59c', 91, 'purchase_orders', 'printReceipt'),
(299, 'ecdbb036cd07e363e005e6edf71d13e4', 97, 'purchase_receives', 'printInvoice'),
(306, 'd36e4c8bea43ccf274c8a206a2fce2c8', 149, 'point_of_sales', 'add'),
(311, '1fd5c3934e7c8baf424b298700d79722', 152, 'point_of_sales', 'void'),
(312, '4dbd1d007d28123fcbc60d676a091bb9', 153, 'reports', 'accountReceivable'),
(313, '5810b2e6ab184d9e65f3e66283b9ec99', 153, 'reports', 'accountReceivableResult'),
(314, 'c16c282d775e8d2eff4a81dc43420783', 154, 'reports', 'accountPayable'),
(315, '7e256cde58c88cfb7b357cf851ca5e11', 154, 'reports', 'accountPayableResult'),
(322, 'e8d29142f49bff79abac95e0c12c61cc', 149, 'point_of_sales', 'getProductByCode'),
(323, '53d37b5678b66e3037655c3a74a452cf', 113, 'general_ledgers', 'indexByAging'),
(324, '8d284a7e74a50e969be7fd4f23cecfc2', 113, 'general_ledgers', 'ajaxByAging'),
(325, '8792c5531904b5401b884ef7cd4e7ad6', 155, 'reports', 'purchaseOrderBarcode'),
(326, '492395fc340a81f506a927d32e2f1afe', 155, 'reports', 'purchaseOrderBarcodeAjax'),
(327, '6beb918c894f216f84186fc6e040f6c2', 149, 'uoms', 'getRelativeUom'),
(330, 'e9da151e8018c8df283953086e2a952d', 113, 'general_ledgers', 'indexByTb'),
(331, '9e3fccd597cc436f5d1746a01aa86c74', 113, 'general_ledgers', 'ajaxByTb'),
(332, '15b6739571d91c42bffeabde8ea8c23e', 151, 'point_of_sales', 'printReceipt'),
(334, 'edeaf437d92c122dbe8504ff2c45ac1a', 117, 'reports', 'generalLedger'),
(335, 'e620e48f75a001ec81673a41298338c4', 117, 'reports', 'generalLedgerResult'),
(336, '6e7f01d4edfd1166d453125a18ca95cd', 117, 'reports', 'generalLedgerAjax'),
(341, 'd5de0cd850f3f74f7dcbe8d987a7ad67', 156, 'budget_pls', 'index'),
(342, '67c866502915d8cbe9c3fd95f09accd5', 156, 'budget_pls', 'ajax'),
(343, 'e34531f9e0cc4ff7116fa68b1211048c', 156, 'budget_pls', 'view'),
(344, 'fb111980d58a74c2c83ac63ece7037d6', 157, 'budget_pls', 'add'),
(345, 'cbe164e9a766a2adbe8746f23c128682', 158, 'budget_pls', 'edit'),
(346, '0f3ce67b308e4d48456bb9f6e87819b6', 159, 'budget_pls', 'delete'),
(347, 'a24ce076c7a9f235f3ffcb053f62d9ec', 160, 'classes', 'index'),
(348, 'fb24e4231724c3bd5032bafba8485783', 160, 'classes', 'ajax'),
(349, '69ae56d3a0b912d244a059802f7929dc', 160, 'classes', 'view'),
(350, 'dd15e1c80387d9074fc88b8b401321fb', 161, 'classes', 'add'),
(351, 'b99cbcdfddebf9040f6880e87cb03bb1', 162, 'classes', 'edit'),
(352, '1ffb6b7550f63bd4d0e06a401d000154', 163, 'classes', 'delete'),
(354, '98f1a02eb4e3254098709ea295f1dfe4', 134, 'reports', 'productAgingAjax'),
(355, '8b4b3d2dfa6fcd77ced3b973f3a6cc91', 165, 'reports', 'productCycle'),
(356, '5c7fd1d0e97c5cec1e090c37850dab3b', 165, 'reports', 'productCycleResult'),
(357, '1c66cfe5b6670809332c423506f6c0b5', 165, 'reports', 'productCycleAjax'),
(358, 'c87d69d6c4b8bde602dae73f8bf4f4d4', 166, 'reports', 'productAverageCost'),
(359, '4a6de0202e09cce282ad5da749a506e4', 166, 'reports', 'productAverageCostResult'),
(360, 'e8d4e94eb840ab24b3d4c2bde02fe5bd', 166, 'reports', 'productAverageCostAjax'),
(361, '475c98eb438eb69a9562a3857c5f3e7c', 167, 'reports', 'productPrice'),
(362, 'da9b78f774891613773a99ff7ba0cb19', 167, 'reports', 'productPriceResult'),
(363, '117ef731b08e0a7c57aef3d2e91a27e2', 167, 'reports', 'productPriceAjax'),
(370, '1ec2fc9e4da879daafa269e7f3b150f4', 168, 'reports', 'customerDormant'),
(371, '8870eff80bd1d982063fb2f771814524', 168, 'reports', 'customerDormantResult'),
(372, 'cba14eb5f8f83feb58242ac9f79caac9', 168, 'reports', 'customerDormantAjax'),
(373, 'fbba48b30f3c44370dd8649df736a1c7', 169, 'reports', 'customerDiscount'),
(374, '02f82dc926bd22c5d06f1664a29a1a2b', 169, 'reports', 'customerDiscountResult'),
(375, '747a6c296c658e091e5405fdb74c8748', 169, 'reports', 'customerDiscountAjax'),
(376, 'f3ae99182e788e82db7bc96942aed75b', 170, 'reports', 'userRights'),
(377, 'd1e3e0a86d30af3533a744c81ae0ba10', 170, 'reports', 'userRightsResult'),
(379, 'a8a06fd08688948429780596a40c748d', 171, 'reports', 'userLog'),
(380, 'f5f1ed86faa4e2d962ff2ed98b2148b4', 171, 'reports', 'userLogResult'),
(381, 'd1b42a846fef4980938aaf967729a7c0', 171, 'reports', 'userLogAjax'),
(382, '66e705d131f9928f1ed5f8187a75b470', 172, 'reports', 'vendorProductList'),
(383, 'cce96c3138ed6e2c3304efe0f893fceb', 172, 'reports', 'vendorProductListResult'),
(384, '99faabc32c87d4ec8db2b8c2c99a89db', 172, 'reports', 'vendorProductListAjax'),
(385, '62e53841f916452a9ff7dff76a8ac502', 173, 'reports', 'vendorAddress'),
(386, '2ab95076a92e23bff5952d13c4a63b07', 173, 'reports', 'vendorAddressResult'),
(387, '2da3892d01653bcf8fa807643d462f37', 173, 'reports', 'vendorAddressAjax'),
(388, '0964e719422d8d884f248fcb37b6b3c8', 175, 'reports', 'productScrap'),
(389, 'f5bcdff53b9b16253fa3aae6d20c9835', 175, 'reports', 'productScrapResult'),
(390, '97641518532eebfa41cb42d66664b567', 175, 'reports', 'productScrapAjax'),
(391, 'e023607774dcfa209c75aeedd7f48208', 174, 'reports', 'vendorAddressList'),
(392, '170eee80c2f98c3dc1fcef9ac4abc1c0', 174, 'reports', 'vendorAddressListResult'),
(393, '937a7d658b00c3cd0fe15f8132ac555e', 176, 'settings', 'ics'),
(394, 'dba28f8b87a644c730b70fb497ef7e96', 113, 'general_ledgers', 'indexById'),
(395, 'dc63da1fdf14b25261fc866c7876092b', 113, 'general_ledgers', 'ajaxById'),
(396, 'ed5ccae2e7e4cafe2ef0edfe8c320ff9', 113, 'general_ledgers', 'indexByGroup'),
(397, '7a3b6118db3bf877d9ac092dca883c19', 113, 'general_ledgers', 'ajaxByGroup'),
(398, '1de161d9014d90ae611da3ba3bebbd81', 177, 'general_ledgers', 'writeChecks'),
(399, '4d10369ada03fdaa942c39b38f5a191e', 177, 'general_ledgers', 'getBalance'),
(402, 'bd92f63a483bd86561a806457d81c37b', 179, 'reports', 'productMove'),
(403, 'e19563cafccb44eb32595df6cedb6a35', 179, 'reports', 'productMoveResult'),
(404, '33afe47f0fa83ca56ef5f8c5ab4f88d0', 179, 'reports', 'productMoveAjax'),
(408, '72d7fdd02ef0c62073c03c581c1256c6', 182, 'point_of_sales', 'changeDiscount'),
(420, 'b1d8df03d5d3539f4d76c23f931b2eff', 186, 'general_ledgers', 'indexAll'),
(421, '99b2ffee9cb4becc4bf9045bffd4a120', 186, 'general_ledgers', 'ajaxAll'),
(422, '0174ddec1ec9c15a4b078df58aebe222', 186, 'general_ledgers', 'viewAll'),
(423, '336a49511bb35aea96666d7c4a0bf0ef', 187, 'general_ledgers', 'addAll'),
(424, '29237c362ee2462066a30d1ad1e68085', 187, 'general_ledgers', 'checkCompany'),
(425, 'a8ca08be398b033025fe46f6cf2b103d', 188, 'general_ledgers', 'makeDeposits'),
(426, 'c7f6d450ea8bf050458c400a1171c3eb', 188, 'general_ledgers', 'checkCompany'),
(427, '472e1be92b6901be37eb55e7dde28865', 189, 'general_ledgers', 'editAll'),
(428, '4be2b1adf2cbe1e31e4cd8fd8a84e1e6', 189, 'general_ledgers', 'checkCompany'),
(429, 'b1aa28dcf3f685496c6011a923691026', 190, 'general_ledgers', 'deleteAll'),
(437, '48b35ea70c9f72715f2020ce0642d392', 191, 'reports', 'productInternalUseResult'),
(438, '5078bd5fb9690078da4cccb7e1f8d0e2', 191, 'reports', 'productInternalUseAjax'),
(439, 'e0ab5f21736269976e21305382222e9d', 192, 'reports', 'customerInvoice'),
(440, '97aef41495c642a0a53d2bdbecfb3a99', 192, 'reports', 'customerInvoiceResult'),
(441, '311c12567f74e16177882ec5be45f7e1', 192, 'reports', 'customerInvoiceAjax'),
(442, '304acc171054c1789dc6733b2545c78b', 193, 'payment_terms', 'index'),
(443, 'd7d811c97a6d5c35841adf66e553f2a0', 193, 'payment_terms', 'ajax'),
(444, '1eb70e10199725e1f12123834613aba3', 193, 'payment_terms', 'view'),
(445, '36a2771cb6b4b45fad49f090d850588a', 194, 'payment_terms', 'add'),
(446, 'e8de22a1c5d170296b8ee17377e8dfc8', 195, 'payment_terms', 'edit'),
(447, 'ec778d730190d2ca96a8ace204b2e486', 196, 'payment_terms', 'delete'),
(450, 'f4a4aa50a430db91b3a24b3c7c4c5d54', 197, 'reports', 'product'),
(451, '9720d2bf4d5325e9d6054e67ced1c5c6', 197, 'reports', 'productResult'),
(452, '5e8f36f3588166347c6fccd93a08d722', 197, 'reports', 'productAjax'),
(453, '69bb970fd7cbf9100c5d1e93e7d9a34e', 198, 'reports', 'purchaseInvoice'),
(454, 'c1ff2c2d317cc9af7fb6f9a15972edfb', 198, 'reports', 'purchaseInvoiceResult'),
(455, 'fee572ad940b1c25cd8e4aeb38d2b5c0', 198, 'reports', 'purchaseInvoiceAjax'),
(456, '98f9d09dc453fd6f3e2458a35e60bef7', 199, 'chart_accounts', 'delete'),
(457, 'cd2e8a5c1f7844cdcd379c2c87879184', 200, 'chart_account_groups', 'delete'),
(461, '12c8f0649dd07670dd2334b7d6fb5316', 201, 'purchase_orders', 'edit'),
(462, '7f4ecb8532d17d27da6c8c21ae2dffa9', 202, 'credit_memos', 'index'),
(463, 'fb05f02aee2f4266210741ce4be60695', 202, 'credit_memos', 'ajax'),
(464, '95cb1a65e05f9dcb601916a14febf1bd', 202, 'credit_memos', 'view'),
(465, '5b043fa1775e0f94ca6cb2c8a8447bfd', 202, 'credit_memos', 'printInvoice'),
(466, '6a7a6f04f68d0502635dc6f23c86c832', 202, 'credit_memos', 'printReceipt'),
(467, 'bccec59fd4704fb7ccd49504a8b4dd59', 202, 'credit_memos', 'printReceiptCurrent'),
(468, '79dd5288ffd78d69d4053266ab8e7fb2', 203, 'credit_memos', 'add'),
(469, 'e9e21d475beeaf9ebe0733ef2ceea60d', 203, 'credit_memos', 'orderDetails'),
(475, '92dd2967bfa2a825e286e2226392fdec', 202, 'credit_memos', 'searchProduct'),
(476, '530a85e1009e710cc477600eda294eba', 202, 'credit_memos', 'searchProductByCode'),
(477, 'bf8fae8e5f77536a6f28bda85d708a74', 203, 'uoms', 'getRelativeUom'),
(478, 'e5932c11b0353d3e94c6baa2e3a22988', 202, 'credit_memos', 'product'),
(479, '125407b580d3f95df46f73b3f72b490e', 202, 'credit_memos', 'product_ajax'),
(480, '4ddef1b4afa098a4ea6dcfd74895df4b', 202, 'credit_memos', 'customer'),
(481, 'c681b25e4e27ff75964a39716dabe2da', 202, 'credit_memos', 'customer_ajax'),
(482, 'b264924bcb992599707c70df3ab2e01e', 204, 'credit_memos', 'aging'),
(485, 'e3d2e2ccf37ec7e9490f2a2f7e334127', 205, 'purchase_returns', 'index'),
(486, '35c4953e4fa1550d187c6889f6a1ffbf', 205, 'purchase_returns', 'ajax'),
(487, 'cc0964f1b7186d1bd35b9b313c4ce855', 205, 'purchase_returns', 'view'),
(488, '6be4b6fc1ec504c3c84e9f3d9c19185f', 205, 'purchase_returns', 'printInvoice'),
(489, 'cf8f4bd3b41feb706eaab5c1bf49c24b', 205, 'purchase_returns', 'printReceipt'),
(490, '7f8088e761e841d94b628afd9e924443', 205, 'purchase_returns', 'printReceiptCurrent'),
(491, 'ee4e887f9571b7963e8b350758e800a9', 206, 'purchase_returns', 'add'),
(492, '7e275439c6f288e5e756cab70dd583be', 206, 'purchase_returns', 'orderDetails'),
(500, '8b936620227292075fa88e930cd7f998', 206, 'uoms', 'getRelativeUom'),
(505, '8dd115eab413b76e93f2cb5e85d370c1', 207, 'purchase_returns', 'aging'),
(508, '364439fe3d56dcb3943d179d331b6082', 208, 'settings', 'accountClosingDate'),
(509, '9fbddfe71da996d3312412ac3a765a80', 209, 'receive_payments', 'index'),
(510, 'bfa6dadd8a957327263c0e6d61868c38', 209, 'receive_payments', 'ajax'),
(511, 'dfc91e752dad631029971341f0b21e96', 209, 'receive_payments', 'save'),
(512, '25bbc5019c541ffa4cd2cccf64bb7a99', 210, 'pay_bills', 'index'),
(513, '9b1e5b609acd043e27f48d31c7f0d5d2', 210, 'pay_bills', 'ajax'),
(514, '6fca9f77f2f4f747033da21e26ec9ff6', 210, 'pay_bills', 'save'),
(519, '6e88bb7bc1fa7f9ba66f3c9dc199f1b8', 212, 'reports', 'customerInvoiceCredit'),
(520, '64ac275f8896ba45bc40cae8a937f17f', 212, 'reports', 'customerInvoiceCreditResult'),
(521, 'd5f76371aa2f0e01743936ccdd837093', 212, 'reports', 'customerInvoiceCreditAjax'),
(522, 'ea8c7917feca6a11963f1437b1e4f1d4', 213, 'reports', 'purchaseInvoiceCredit'),
(523, '0a74800f8283e62ec1655a07067433b3', 213, 'reports', 'purchaseInvoiceCreditResult'),
(524, '6cb7a2d99da41b845cbf9f136d46f9a3', 213, 'reports', 'purchaseInvoiceCreditAjax'),
(525, '66e906ec3aea13f3cce24d655c9f95a0', 197, 'reports', 'productViewQtyDetail'),
(526, '2eb1d5f57ff7726b30dcb1fe731a3c65', 214, 'reports', 'customerBalance'),
(527, '55d7c8c88433a2a6ad77e8f0e5cd9331', 214, 'reports', 'customerBalanceResult'),
(528, '432923669a47d64eb323170822a3eed6', 214, 'reports', 'customerBalanceAjax'),
(529, 'be630842ffa1ae1fc87a4e4ff3d35f56', 215, 'reports', 'vendorBalance'),
(530, '3bf643c34b9818bc507d01f4dd325064', 215, 'reports', 'vendorBalanceResult'),
(531, '6c9e7b7255217f5ade5d216174da673e', 215, 'reports', 'vendorBalanceAjax'),
(532, '9903ebcd0b03979594f87796330d476d', 216, 'credit_memos', 'service'),
(533, '8e13d0f3ac8697843b9edb9d5057941a', 217, 'credit_memos', 'miscellaneous'),
(534, '01a2553b0bf5ce9c14346e71bc40d668', 218, 'purchase_returns', 'service'),
(535, 'f2d63882dd445786206bd07210cafc11', 219, 'purchase_returns', 'miscellaneous'),
(536, '994884dbc542893e6e6ad31bdb991de6', 204, 'credit_memos', 'invoice'),
(537, '81c8b72a629f27cd46c66bef5692645a', 204, 'credit_memos', 'invoiceAjax'),
(538, '5d4c760dac30a79fdbc92ae487e90883', 207, 'purchase_returns', 'invoice'),
(539, '35a508cf99686b34f6f3a30a1fe35420', 207, 'purchase_returns', 'invoiceAjax'),
(553, '2b97f64071c1900c1fbf26ac1a975a92', 221, 'credit_memos', 'edit'),
(554, 'ccf1edd78453bc5f4669047dfef64c1c', 221, 'credit_memos', 'editDetail'),
(561, '94a84a33b19da0702b8edf35b1001b7c', 222, 'purchase_returns', 'edit'),
(562, '20c4d918cc22f1c7fb0d73b41490d988', 222, 'purchase_returns', 'editDetail'),
(573, 'c3c689e61625b561e23b04842240cd0d', 223, 'purchase_returns', 'void'),
(574, '6bb31c9f5e89b8c9865beda768ffe826', 399, 'purchase_returns', 'receive'),
(575, 'c6d2dbfbb31ae152aa86cd1332c88a05', 396, 'credit_memos', 'receive'),
(576, 'df214bcb68706eca9aa268b2011a51c6', 224, 'ar_agings', 'index'),
(577, '4b76fe9e61ff0d12abede9fda18336ff', 224, 'ar_agings', 'ajax'),
(578, '4709aa528a6f3bed2dd56c3c014838a1', 224, 'ar_agings', 'save'),
(579, '9dd2ed7ca2cd9d05b49a7f1951b6153a', 225, 'ap_agings', 'index'),
(580, 'f9cde12869327cfe6b270c549ca800a6', 225, 'ap_agings', 'ajax'),
(581, '3df44e148acae64e09fb45ad6d1406a1', 225, 'ap_agings', 'save'),
(583, 'a1cc0327dffc644773309d7613a30f95', 220, 'credit_memos', 'void'),
(584, '481fde4ece4ea056e2c46dbc21ece016', 226, 'point_of_sales', 'service'),
(586, '7d28441a1da5a6b05b3ced26714756e2', 399, 'purchase_returns', 'getProductDeliveryByLocation'),
(587, 'a1af9911e161b2d060ad96582a8dcfe3', 221, 'uoms', 'getRelativeUom'),
(615, 'e6c82f42168fe759c3210e6955825317', 211, 'uoms', 'getRelativeUom'),
(626, 'ecb4ad3e66895f9d69f0fb41d18e286f', 201, 'uoms', 'getRelativeUom'),
(631, 'af0c8d8db6d51b28ee5279d166ef21ad', 222, 'uoms', 'getRelativeUom'),
(634, 'f10b43da92957a67081e31c75a0300c5', 1, 'general_ledgers', 'indexByTbDateRange'),
(635, '82c1cc75d679701d88ce4dc51d8713b0', 1, 'general_ledgers', 'ajaxByTbDateRange'),
(636, 'eea88010d0809bc169def9096b56e8f3', 1, 'general_ledgers', 'indexByGroupDateRange'),
(637, '0122298c677019631cbadc9fc5586895', 1, 'general_ledgers', 'ajaxByGroupDateRange'),
(638, '14d365e54208142c9ebcf981978592c4', 228, 'reports', 'posByType'),
(639, '723f42b632992e303be270e8d12e86b8', 228, 'reports', 'posByTypeResult'),
(640, 'fb3999ce30433316ca3f6ef6c37edea5', 228, 'reports', 'posByTypeDetailResult'),
(641, '24502f3a86e4e81c8d51e7044f1f1e4c', 204, 'credit_memos', 'applyToInvoice'),
(642, '2de5aaccf032f4aeefe4cbe5758d37c5', 207, 'purchase_returns', 'applyToPO'),
(643, 'a970cb3b5e87a4015ceccbd0057d8d80', 229, 'reports', 'customerInvoiceByRep'),
(644, '2ac1173c7fa0e14812d14ae39b75a14d', 229, 'reports', 'customerInvoiceByRepResult'),
(645, '7ebe750c822d06ee7ce9c6f8009dc43d', 229, 'reports', 'customerInvoiceByRepAjax'),
(646, '1cf8c16544083f41010d33cd38a4a11d', 230, 'reports', 'customerReceivePayment'),
(647, '07edb6038e3f178596ca80d0b43c2e88', 230, 'reports', 'customerReceivePaymentResult'),
(648, 'a324a25cb23c459d6ed81e0da7a44f74', 230, 'reports', 'customerReceivePaymentAjax'),
(649, '44b7024b6ff22c402f1e6284930ca0a2', 231, 'reports', 'customerReceivePaymentByRep'),
(650, '566ac7380251ade3d7d4150530fde7c9', 231, 'reports', 'customerReceivePaymentByRepResult'),
(651, 'a31d2a35064a2efa6611daa16ef23d1c', 231, 'reports', 'customerReceivePaymentByRepAjax'),
(652, '04c24e40be85d757d73abbb2d26d37ec', 232, 'reports', 'vendorPayBill'),
(653, 'c174c16171cda692e311961695cb7444', 232, 'reports', 'vendorPayBillResult'),
(654, '2c1ac40754d8ccd58283ad201903ee7c', 232, 'reports', 'vendorPayBillAjax'),
(655, 'a25f7078c68c99863b5b4108cf9f4d74', 233, 'others', 'index'),
(656, 'c7d1e1650926ff4719f8a392cb4f5299', 233, 'others', 'ajax'),
(657, '8fec9a5bc09cc8938f5afcf6389e6fad', 233, 'others', 'view'),
(658, '070502da2d6500b4b68f46df020c3e08', 234, 'others', 'add'),
(659, 'b858f74493839347a16f19a73d371205', 235, 'others', 'edit'),
(660, 'a06c9d1bd91f276a2a352a4c16687afd', 236, 'others', 'delete'),
(661, '47fa3b584e503048dae262c09e97f645', 237, 'reports', 'depositDetail'),
(662, 'd5dae23e0c478d5642dbd9179f203e95', 237, 'reports', 'depositDetailResult'),
(663, 'ab222d061bc5a70239ea302ed311de82', 237, 'reports', 'depositDetailAjax'),
(664, '7fbe0dee798172fd6233271e0e223399', 242, 'reports', 'pos'),
(665, '99362853bedb9cb31bbd4d1d6f9cad0b', 242, 'reports', 'posResult'),
(666, '92367c6683a81b3bfcd88df03f0ffc1f', 242, 'reports', 'posAjax'),
(667, '5576a6524e2aa6fb9c5c29b742ee2437', 126, 'transfer_receives', 'receiveAll'),
(668, '83499ae11cf44edbd5725a251c15a745', 238, 'reports', 'posByItem'),
(669, 'd074dd739807dd95146c723e7fbee86d', 238, 'reports', 'posByItemResult'),
(670, '25b79ca5607948ab24a0bfb08cbbfe2d', 239, 'reports', 'salesByItem'),
(671, 'bedf8acc6ee1410b5cee57f7699c01dd', 239, 'reports', 'salesByItemResult'),
(672, '4624884cb306bae3d53d791e0d0b6eca', 240, 'reports', 'salesByCustomer'),
(673, '223281fe9978976082ed2018bf6c1b5c', 240, 'reports', 'salesByCustomerResult'),
(674, '8cfec25159656c432997a410dad458a5', 241, 'reports', 'salesByRep'),
(675, '177f2ff678704e16bc7f1824c892aabe', 241, 'reports', 'salesByRepResult'),
(676, '58fea419470ce028ef4390daef6e4888', 243, 'reports', 'transferOrder'),
(677, 'b2b77c75974d7dffb9f5dbae0fb180e8', 243, 'reports', 'transferOrderResult'),
(678, 'a8a1b5e027fc71379a8af7f3438e2a19', 243, 'reports', 'transferOrderAjax'),
(679, 'b4aa18f1639170571e99eb17b0714e0f', 243, 'reports', 'printReceipt'),
(680, '4dffcfa6f192a0140cc93810154b5e14', 91, 'purchase_orders', 'printInvoiceProduct'),
(682, '07cd4461c4cfa2ac8197145c47daec4a', 238, 'reports', 'posByItemDetailResult'),
(683, 'c5ccca4fc89f88f87b9664e6471bc3ff', 239, 'reports', 'salesByItemDetailResult'),
(684, 'ba1da34bccb75b8f5bdfa4422dce4d98', 240, 'reports', 'salesByCustomerDetailResult'),
(685, 'c2d7faac074cee38177a9b5321f6e14a', 241, 'reports', 'salesByRepDetailResult'),
(686, '7047371a5c217a3743012806f748a20f', 145, 'purchase_orders', 'voidReceipt'),
(687, '3684711682719266ebfdefe4f693eefb', 204, 'credit_memos', 'voidReceipt'),
(688, '1ece276520732cd5ae70a0d2e5967f81', 207, 'purchase_returns', 'voidReceipt'),
(690, 'd3f386aad4a04877ccc70bc84e48560d', 244, 'reports', 'salesByType'),
(691, 'd140bf62ab60100ef6de0d488fbe47ca', 244, 'reports', 'salesByTypeResult'),
(692, '41e7931bb242e9c3413710ce3f849558', 244, 'reports', 'salesByTypeDetailResult'),
(693, '423284b6e1c9d239ba7c4cea6a79c10f', 197, 'reports', 'productByTypeResult'),
(694, 'f492d31e223d2d29c9957fe51b7a3158', 197, 'reports', 'productByTypeAjax'),
(695, 'b42188f38d8f56f3bc00a4c45244036b', 245, 'reports', 'customerAddress'),
(696, '72a2c14d06e067114bf2f1df10d8980e', 245, 'reports', 'customerAddressResult'),
(697, '62c957cdb4c0867b690d12e1d648f29b', 245, 'reports', 'customerAddressAjax'),
(698, '6ae1088d6a048b51191f398ddc6953e5', 114, 'general_ledgers', 'customer'),
(699, '3b98e3777940974f690430f614bce55f', 114, 'general_ledgers', 'customerAjax'),
(700, 'd7056c64738115c3f0ff99777a939bf1', 114, 'general_ledgers', 'vendor'),
(701, 'd6f42c48c241a6c125f1a547410a80c2', 114, 'general_ledgers', 'vendorAjax'),
(702, 'bfdb23a67495a74cbfdcae64a0124983', 114, 'general_ledgers', 'other'),
(703, '5128e5716100adc67b65f9adf5362b9d', 114, 'general_ledgers', 'otherAjax'),
(704, 'fa4d9f06e838fcb6ef10b6d2a1748233', 115, 'general_ledgers', 'customer'),
(705, 'f59860a17df3f443a21a3703412c9357', 115, 'general_ledgers', 'customerAjax'),
(706, 'dc138ccbf5f87af9a38ce92793e92669', 115, 'general_ledgers', 'vendor'),
(707, 'f715e5410241d7a02af858bbca489895', 115, 'general_ledgers', 'vendorAjax'),
(708, '80aa2254ca21d6cfb35485dd996401c3', 115, 'general_ledgers', 'other'),
(709, 'fe26c3672ee9aacf9939a777932d5c73', 115, 'general_ledgers', 'otherAjax'),
(710, 'a1cff5c4048d33899763dc0f3b87ac7d', 177, 'general_ledgers', 'customer'),
(711, '5c2bb2bb47861291a73a99b0b621cb34', 177, 'general_ledgers', 'customerAjax'),
(712, '4a5cb68485d3f99635970ecc3e5bb20f', 177, 'general_ledgers', 'vendor'),
(713, 'e73b51a9f3c1fdd0358e4d209518189a', 177, 'general_ledgers', 'vendorAjax'),
(714, '41de370f1bceaf5395727868b9d1631d', 177, 'general_ledgers', 'other'),
(715, '67c1d45507b4c3e55c5c54037ddf9f34', 177, 'general_ledgers', 'otherAjax'),
(716, '6ef0ebeddf84caaa6b282a259d31f485', 188, 'general_ledgers', 'customer'),
(717, '778d33b2972b294abd96a72b2111adc3', 188, 'general_ledgers', 'customerAjax'),
(718, '6a53079bb06a4da35c4fe3a558fe3016', 188, 'general_ledgers', 'vendor'),
(719, '0103e9f99990087b426e7212a38beaf9', 188, 'general_ledgers', 'vendorAjax'),
(720, 'b265c75790c9ffc87a18af0da51bf0cc', 188, 'general_ledgers', 'other'),
(721, '30b1afef2df071a0220a7afcaf8de98e', 188, 'general_ledgers', 'otherAjax'),
(722, '219539b5147ecb9570ea14e852539a3d', 187, 'general_ledgers', 'customer'),
(723, '49dad5276577a1ff4a5c00efff8ecccc', 187, 'general_ledgers', 'customerAjax'),
(724, 'fb9a5cc646f269e988597033284c0faf', 187, 'general_ledgers', 'vendor'),
(725, '1e3b7ea52ea4bd6f67a5eb0c27027dfa', 187, 'general_ledgers', 'vendorAjax'),
(726, '4fc360f99d43278a73db11e6e271ca36', 187, 'general_ledgers', 'other'),
(727, '2bcaf2280440c44edf3ef6ccc5e478c6', 187, 'general_ledgers', 'otherAjax'),
(728, 'ecc85835e53a1340df0bac9b5cab4307', 189, 'general_ledgers', 'customer'),
(729, '4cc56ed77d7b1ce7b5a095622427a24c', 189, 'general_ledgers', 'customerAjax'),
(730, 'bed79f473b2ffe55b2b327403bb576f9', 189, 'general_ledgers', 'vendor'),
(731, 'd629341be66eb9152a5df5d8e7162a7a', 189, 'general_ledgers', 'vendorAjax'),
(732, '9b0a519e9e6adf664f9daea1f57af430', 189, 'general_ledgers', 'other'),
(733, 'c14d556bfe6305f4cc7dd2f2f007168a', 189, 'general_ledgers', 'otherAjax'),
(734, 'b392a00614a6a80c081163804a479856', 260, 'purchase_requests', 'service'),
(736, '1b8756e37efefc0ffab72527a52cb013', 246, 'inv_adjs', 'index'),
(737, '643810970ed6efc8463f46d03d4279e6', 246, 'inv_adjs', 'ajax'),
(738, '596622b0d9e53af80c9ac7ce491df2a9', 55, 'inv_adjs', 'save'),
(739, '317908fb6570f49230653abe2ee27b45', 247, 'employees', 'index'),
(740, 'a41f5abddbbe0076dd39f302129e77b7', 247, 'employees', 'ajax'),
(741, 'b117656b694344ae46cd7c29b1bba428', 247, 'employees', 'view'),
(742, '8ba37ba16b53c013f62fee8714cf884f', 248, 'employees', 'add'),
(743, 'd0554beb1228cf4ebe669ebee470f690', 249, 'employees', 'edit'),
(744, '0bf0f245ce7ac907f34c7b8707c6cb09', 250, 'employees', 'delete'),
(745, '30ac53fc0d7547d1059add41762973f6', 114, 'general_ledgers', 'employee'),
(746, '524cb86f6a4635b7e6286c8467b93dc1', 114, 'general_ledgers', 'employeeAjax'),
(747, 'b973e9eb119979f084d4040dbcb401d3', 115, 'general_ledgers', 'employee'),
(748, 'cc4d390ffaf3d2cde2a34cb9d4590db5', 115, 'general_ledgers', 'employeeAjax'),
(749, 'ce735eb24fe5e2a16b715fc5ca2679e1', 177, 'general_ledgers', 'employee'),
(750, '304772d74e2334ee610cd35b685c3d4f', 177, 'general_ledgers', 'employeeAjax'),
(751, 'a2eca7926b55ab50030134425f836c04', 188, 'general_ledgers', 'employee'),
(752, 'e8406313ab6d17ed28e5f22b8eeb5138', 188, 'general_ledgers', 'employeeAjax'),
(753, 'd884d7bb15b0317580a749eea44c7065', 187, 'general_ledgers', 'employee'),
(754, 'eeab28d1bbbc21ceef39cc8645465627', 187, 'general_ledgers', 'employeeAjax'),
(755, '0b8280abb3b8110c0a55263818e48d8c', 189, 'general_ledgers', 'employee'),
(756, '0029401741880c4b00fa2556197e934f', 189, 'general_ledgers', 'employeeAjax'),
(757, '80a5cf85477c4f7861ad8613525bc323', 251, 'reports', 'inventoryValuation'),
(758, '3d988df11249409ff20fc725dfc4dde6', 251, 'reports', 'inventoryValuationResult'),
(759, 'b6868abc28892fb99580dca86ebd704e', 251, 'reports', 'inventoryValuationDetailResult'),
(760, '5cc30ecf1c264322bcef4838c93d22f6', 246, 'inv_adjs', 'uom'),
(762, '3da73235a4cb0f303c3d6fac4bd7f736', 238, 'reports', 'posByItemParentResult'),
(763, 'e69961d6878e732c9db65b1b2a2341ba', 228, 'reports', 'posByTypeParentResult'),
(764, '44760bd9f4c5ae4c50ac2802afcbaed6', 239, 'reports', 'salesByItemParentResult'),
(765, 'f196f33a145a51008bb080fab8d61069', 244, 'reports', 'salesByTypeParentResult'),
(768, 'b1e0359ca423fdaf69945095142113a3', 253, 'purchase_requests', 'index'),
(769, 'd0ec63ed61304b0bf15dce4dad8cc00d', 253, 'purchase_requests', 'ajax'),
(770, '8bd226a6ca5e648bac221d4ccc2f50e7', 253, 'purchase_requests', 'view'),
(771, 'b76db18f8f7448e7cc8f7abb71acfa4a', 254, 'purchase_requests', 'add'),
(772, '80d4989471e09732401f53f456b64d09', 253, 'purchase_requests', 'product'),
(773, 'fe15d0bd701fe7520db94cc426c4a9d1', 253, 'purchase_requests', 'productAjax'),
(774, '1741cc80bfda68e92fe1d8b6c705a52c', 253, 'purchase_requests', 'vendor'),
(775, 'd16e5bed8c8b06ab872722d53af5f841', 253, 'purchase_requests', 'vendorAjax'),
(776, 'ba4de4d2af69c2b8b82b5c82074a335c', 255, 'purchase_requests', 'edit'),
(777, 'b2d3e9bdb20315f7dfcc5ded55284eab', 256, 'purchase_requests', 'delete'),
(778, '2d08c6132c148d58ba8c30a2707a93e6', 253, 'purchase_requests', 'printInvoice'),
(787, '93a629c183ebd90cf03eb4a1de13fc20', 253, 'purchase_requests', 'purchaseRequestView'),
(789, '0c0c60063216bf315909921fb1f3ca06', 246, 'inv_adjs', 'printInvoice'),
(790, '396e132fa8e9acb26695be1e3b1477ea', 257, 'reports', 'inventoryActivity'),
(791, '8454037a1623ed0cb691fd20e05d7454', 257, 'reports', 'inventoryActivityResult'),
(792, '6169d4337e250254872099054819bf39', 258, 'reports', 'inventoryAdjustment'),
(793, '27503936bb24fb82e6c987b3b4f65eb7', 258, 'reports', 'inventoryAdjustmentResult'),
(794, '243276cf0bcae6a3516a9660daab6aac', 258, 'reports', 'inventoryAdjustmentAjax'),
(796, 'f3c5ed69acc0ab335b5cb0cc5be58f85', 55, 'inv_adjs', 'add'),
(797, '77be65afbbbf821b0a3fda84251e9d9e', 55, 'inv_adjs', 'addDetail'),
(798, 'cdbd9ae9672ac407955f54f86489a365', 88, 'inv_adjs', 'edit'),
(799, '143072da6be5fe1e24d3aa6d9f7aaeda', 88, 'inv_adjs', 'editDetail'),
(800, '653632c32642f47a303e492460d8031b', 322, 'inv_adjs', 'delete'),
(801, '5355396a2c510c8f13fc4d0d79055b2e', 323, 'inv_adjs', 'approve'),
(802, '752f4e125ce87346c65a937634c06389', 88, 'inv_adjs', 'saveEdit'),
(803, '3e0343281a8ad4cfc7cbe6535aa822d3', 259, 'credit_memos', 'discount'),
(804, '1ebb962a8b936ea7a7297f6b45fd29c5', 224, 'ar_agings', 'printInvoice'),
(805, '51674558fcd4dcfed8578f17d5742a36', 225, 'ap_agings', 'printInvoice'),
(806, 'da32356c29a81e265ea4cfd082030d42', 263, 'general_ledgers', 'status'),
(807, '26b80b4b723f89178e833f27facce1e5', 113, 'general_ledgers', 'printJournal'),
(808, 'be944434c72cf0e07bb9e21949c1e48c', 113, 'general_ledgers', 'printCheck'),
(809, '4c333cc7304377cba0ee10b3036af31a', 113, 'general_ledgers', 'printDeposit'),
(821, 'b05add404044c848d5c153995ba0727d', 270, 'reconciles', 'index'),
(822, 'e2185a838bf62e12c32a70051cfcba91', 270, 'reconciles', 'ajax'),
(823, 'fb71878a16032072c3bc76d51ac2c673', 270, 'reconciles', 'save'),
(824, '6c0bcf42fd287d7dee2f1d8427b05d08', 186, 'general_ledgers', 'saveNote'),
(825, '94a262af2f025953532cf78d8e0cdbcf', 271, 'egroups', 'index'),
(826, '803e64b3b930291f779f30e99275bf6b', 271, 'egroups', 'ajax'),
(827, '8119c0521b799d76abcfacb099a62d01', 271, 'egroups', 'view'),
(828, '45c09c38caf0c06b3affac9d30a8030a', 272, 'egroups', 'add'),
(829, '239dae0de773edd2f0f2432e3bb75ce1', 272, 'egroups', 'employee'),
(830, 'd33c021734fe112e39b460921f60d1e9', 272, 'egroups', 'employee_ajax'),
(831, '61a878836858ab1456dc34da88c50af7', 273, 'egroups', 'edit'),
(832, '46b589f7682d25bc4057157808fb6a80', 273, 'egroups', 'employee'),
(833, '3f27bff640f1314b65375d8b76a803f4', 273, 'egroups', 'employee_ajax'),
(834, '91f744c0b2f8701108ca6745141a9193', 274, 'egroups', 'delete'),
(836, 'be53422633a3af8a4a14093c94eea4ef', 267, 'reports', 'deliveryResult'),
(837, '397ccb9456a323fdf2a08c0ce5745402', 267, 'reports', 'deliveryAjax'),
(838, 'ca26f38949d63e9cc0653ad592440bb0', 265, 'reports', 'invoice'),
(839, 'add18519d3016dd1b5a3f8d4731b88a8', 265, 'reports', 'invoiceResult'),
(840, '55fb8da2ce22740d111f7075c6183844', 265, 'reports', 'invoiceAjax'),
(841, 'd868811150b18f849140511cffab418a', 266, 'reports', 'openInvoiceByRep'),
(842, 'b7edc0f5f29192f161e890cfd039991d', 266, 'reports', 'openInvoiceByRepResult'),
(843, '84a27445c1a1bba8760c9af9316814a8', 264, 'reports', 'transferByItem'),
(844, '9891a672fe22ac3781a52d62ab714141', 264, 'reports', 'transferByItemResult'),
(845, '95a0833efb84335017ba55bc130f6874', 264, 'reports', 'transferByItemParentResult'),
(846, '24d4a515b2e107aecf33564cd44df9f1', 264, 'reports', 'transferByItemDetailResult'),
(847, '5a2b1163953453e6e9b7089f8c002a78', 268, 'reports', 'purchaseByItem'),
(848, '6a438e5add67abf5b1bde1c6ce8dd217', 268, 'reports', 'purchaseByItemResult'),
(849, '073a001d84d9c80ba9e2d93322bf0a46', 268, 'reports', 'purchaseByItemParentResult'),
(850, '93dcac58a63da8d881e1a66761fb61cb', 268, 'reports', 'purchaseByItemDetailResult'),
(851, '2db0ac2835f11180167f2381894e0fe4', 269, 'reports', 'checkDetail'),
(852, '3b6c475666165d347389516de99e754d', 269, 'reports', 'checkDetailResult'),
(853, 'f1b2d9ff3ec02ffc01293457b31b2a86', 269, 'reports', 'checkDetailAjax'),
(857, 'a7ac3bb3c295fdc2ea6ee6dd9e31fa71', 204, 'credit_memos', 'deleteCmWSlae'),
(858, '38e5854402659e7fe7109d82d2c28ad5', 204, 'credit_memos', 'deleteSlae'),
(859, '48ee3b95b9f428b30f7916e3fbc86e9d', 258, 'reports', 'inventoryAdjustmentByItem'),
(860, '8fa0cb6b80a19202fff05c801cd92b6b', 258, 'reports', 'inventoryAdjustmentByItemResult'),
(861, 'd10559024d04b115f9233683100585bd', 258, 'reports', 'inventoryAdjustmentByItemParentResult'),
(862, '1bdc10fca98fdf32a29c0a13de248618', 258, 'reports', 'inventoryAdjustmentByItemDetailResult'),
(863, '9c7b6c7339dbad6d3272b86b8386eedb', 276, 'fixed_assets', 'index'),
(864, '3645f7cafd162c142bf8f2a9c2e09e64', 276, 'fixed_assets', 'ajax'),
(865, '85b6126e707c331133b28faf4512d0c7', 276, 'fixed_assets', 'view'),
(866, '876fd64710f35c1bdd9003779c707aab', 276, 'fixed_assets', 'add'),
(867, '4ecd51665db0d5a372f927bd000c91e4', 276, 'fixed_assets', 'edit'),
(868, 'ba00728d963e800ae587e5527e694501', 276, 'fixed_assets', 'delete'),
(869, '7f2f5f3b82c2b63b7c3b439b3d022673', 276, 'fixed_assets', 'post'),
(870, '18cd4b9128291684cbf5b966e18aeeab', 276, 'fixed_assets', 'postDetail'),
(871, '12e8b1d4fde075f9e3f3cc264026983d', 276, 'fixed_assets', 'save'),
(872, '4ebdada89698dbc4a1dbe49737dc0d3d', 253, 'purchase_requests', 'searchProduct'),
(873, '42acba2ed88648bd77c3921a7ec543f5', 253, 'purchase_requests', 'searchProductCode'),
(874, '5e37e4630e70cf2a4e1a6943745486df', 257, 'reports', 'inventoryActivityParentResult'),
(875, 'de046ac7419006b2290bbd441e0e729d', 257, 'reports', 'inventoryActivityDetailResult'),
(876, 'df714816efe68d377b51847176a8849e', 257, 'reports', 'inventoryActivityWithGlobalResult'),
(877, '2914911aa72b1d6706a7d3eeacda6e32', 257, 'reports', 'inventoryActivityWithGlobalDetailResult'),
(878, '01101d371b34f51be811f49047d94062', 270, 'reports', 'reconcile'),
(879, '210cbd8173eb06477d4b00f8ac8469a2', 270, 'reports', 'reconcileResult'),
(880, 'c37a3a3b3985b67b2945f6bcf022408f', 270, 'reports', 'getStatementEndingDate'),
(881, '5057f14f5105c8ad99c171c6b9576f8e', 117, 'reports', 'ledger'),
(882, '03408ac19f67ac7f363efa17f325e2ee', 117, 'reports', 'ledgerResult'),
(883, '51e50cb403dfd272fec4a01824fc8068', 117, 'reports', 'ledgerAjax'),
(884, 'd80330700ff8069acd37c11e8492fd0c', 224, 'ar_aging_employees', 'index'),
(885, 'c0339278a4cd91c4a1fd6a4a410a04d0', 224, 'ar_aging_employees', 'ajax'),
(886, 'fb356f13f8f79ed14f65a73a811f6aaa', 224, 'ar_aging_employees', 'save'),
(887, 'dce0293804fd365c6c163af36e57c2b8', 224, 'ar_aging_employees', 'printInvoice'),
(888, '0c0a8ed60c6050565ac4fd02ccb95fcf', 277, 'reports', 'accountReceivableEmployee'),
(889, 'e63f982eab4901fca1809ccd432b96d5', 277, 'reports', 'accountReceivableEmployeeResult'),
(890, '8d1b92f11f902e924e8d837cec1226a4', 278, 'reports', 'employeeBalance'),
(891, 'dacb91dd9c2827222178b8587a63a4c5', 278, 'reports', 'employeeBalanceResult'),
(892, 'd0bfa14d7068f482ca4ad9b586adc2b6', 278, 'reports', 'employeeBalanceAjax'),
(893, 'f094b6b401662f4bef77d55e5795560c', 279, 'settings', 'retainedEarnings'),
(894, '0563932344c26cfb076637214d79039d', 279, 'settings', 'getNetIncome'),
(895, 'fc4278b39bb9ca2ee8f18845c4d420a5', 46, 'products', 'getSkuUom'),
(897, 'f91242af8b6fe2d56c5a6432ccbedb00', 253, 'purchase_requests', 'searchVendor'),
(898, '92832d3a957aa957557c7ed203f88375', 136, 'products', 'productPrice'),
(906, '70c26d2011b38e4e58aeb4902623a558', 399, 'purchase_returns', 'pickProduct'),
(907, '93eb3d3002c2324009dc7a794b603da9', 399, 'purchase_returns', 'pickProductAjax'),
(908, '61cbeaf8be5bd1241032ec48ac714ac8', 399, 'purchase_returns', 'pickProductSave'),
(909, '0f6ea48b0fc04820db578c9b03db136f', 126, 'transfer_receives', 'addReceiveCrontab'),
(912, '31ea8767fbe514f7117fbc93df1d0116', 46, 'products', 'checkSkuUomEdit'),
(913, '26389053d9666dd73247fcd1c45ffb34', 46, 'products', 'checkPucEdit'),
(914, 'c0f31e191d3e4ea245185e7dbcb90d75', 46, 'products', 'checkPuc'),
(916, '3f8c5d008aa74dee8e7ca2444a12b2d0', 46, 'products', 'checkSkuUom'),
(917, '7cecf246ed161c079fe98b2806dce7e3', 46, 'products', 'checkSkuUomEdit'),
(918, 'f0c2bc07e3cecc00943d9e69d29605b2', 46, 'products', 'cropPhoto'),
(919, 'df2411affa82fbab9347a3db8892fa1f', 46, 'products', 'upload'),
(920, 'f16242eb3845040dd48fcce4d057bc91', 46, 'products', 'product'),
(921, '77f27088c2416e93e6e93d27b4045d86', 46, 'products', 'productAjax'),
(922, 'da1f29247392e2af6ef871fc72759783', 207, 'purchase_returns', 'applyToInvoice'),
(923, '0c8d00040740f766f4e2f6ed58b97006', 280, 'point_of_sales', 'productDiscount'),
(924, '1d67bb860e67e29b0080e3bbde69b9de', 207, 'purchase_returns', 'deletePbcWPo'),
(928, '65d95965471efc7e9d20d881e58a58b0', 284, 'deliveries', 'pickProduct'),
(929, 'fb1a217db44dc9120294a067ad824127', 284, 'deliveries', 'pickProductAjax'),
(930, '62050911526616b6f088902d20e437f1', 284, 'deliveries', 'pickProductSave'),
(933, 'cac17d047dc2c25b9436ba5363edf2e5', 282, 'deliveries', 'index'),
(934, '24d8cafd83b82a7a9b0e213cfb7f1a27', 282, 'deliveries', 'ajax'),
(947, '74f20a4c528ce6ec6c1029d9a412629a', 267, 'reports', 'delivery'),
(948, '0ffd2e6f83bfb37b4dbaa0e46c3364a5', 286, 'vgroups', 'index'),
(949, '685fb0e3b50bfbf843cec3f69178f3c2', 286, 'vgroups', 'ajax'),
(950, 'd18d2125b19d44a4fe40be488218f19b', 286, 'vgroups', 'view'),
(951, '19cc38d01c20378fe382baf9507248d4', 286, 'vgroups', 'vendor'),
(952, '51c09db42c544dba042120489f43f235', 286, 'vgroups', 'vendorAjax'),
(953, '4580d00575f69f200da974ff24cd9011', 287, 'vgroups', 'add'),
(954, 'dbf17f476bdcace826918c51a4fb1bdc', 288, 'vgroups', 'edit'),
(955, 'fd6bd98eb88c067f00e8cf832b4b1728', 289, 'vgroups', 'delete'),
(957, '256b2c62d47c9109021563dbd65eb117', 291, 'credit_memos', 'editUnitPrice'),
(958, '61874efb6089742c2366559eb2251cef', 247, 'employees', 'searchEmployee'),
(959, '055b10be1cfa97b11aba5ad733a4172e', 292, 'price_types', 'index'),
(960, 'c94f6bafc76aed72e58a76296e9f4989', 292, 'price_types', 'ajax'),
(961, 'd158703a3ca4abd1113ff90ecc338415', 293, 'price_types', 'add'),
(962, 'c7f475046e16a0cff24e7900b6d4c32f', 294, 'price_types', 'edit'),
(963, '695f1b3e2f350b2a657497789dbbff1a', 295, 'price_types', 'delete'),
(964, '4c984c822553f0fa8607a2c0cdc84e29', 296, 'products', 'setProductPacket'),
(965, 'aa4003d2a6e37379a67ec246aa495e9f', 297, 'uoms', 'exportExcel'),
(966, 'fad7c32a6f86dc63256f89e28d972fc8', 298, 'uom_conversions', 'exportExcel'),
(967, 'ae5901a620e67220176422a546c5a000', 299, 'locations', 'exportExcel'),
(968, '734e9a26f451202419903142f87add35', 301, 'employees', 'exportExcel'),
(969, '956ff3ebb055b460c5e086f0adae224c', 302, 'egroups', 'exportExcel'),
(970, '323f876f08558cc1f4fcb464da8ddbdd', 303, 'payment_terms', 'exportExcel'),
(971, 'fd0e6fa8119bac2afbdfb67f03c9fed3', 304, 'others', 'exportExcel'),
(972, '28cd291c6fb3d3aa45554a3f1d77e6ba', 305, 'classes', 'exportExcel'),
(974, 'ca3e06ff0518ecbc35e5fbbd99cb2055', 307, 'cgroups', 'exportExcel'),
(975, '0f51528f1d7f0894c35a96903a76b2b5', 308, 'vgroups', 'exportExcel'),
(976, '1a6f78776ff274c88bcdf801dd2ff5ee', 309, 'vendors', 'exportExcel'),
(977, '21f992de6fa4434b2ce28932b9510cfa', 310, 'sections', 'exportExcel'),
(978, 'd3486edb9fc5fcab9fb8906bc6ca291c', 311, 'services', 'exportExcel'),
(979, '5027616370a82a0c73da7351b3486bb4', 312, 'pgroups', 'exportExcel'),
(980, '3b44f609df795501c91a027fa55363ba', 313, 'products', 'exportExcel'),
(981, 'bfd7bc269ee17ea559d1ec3d4cdd21bb', 314, 'chart_account_groups', 'exportExcel'),
(982, 'da1ae2574fb445428009e5ef6d5d492a', 315, 'credit_memos', 'viewByUser'),
(984, '9d4ace955045b9492b8fb3131eab2dae', 317, 'purchase_orders', 'viewByUser'),
(985, '0e2fc0c63aa9e58b1503922a6e5f0632', 318, 'purchase_returns', 'viewByUser'),
(986, '3e61f78b6612d0a7caa490203f52488a', 319, 'deliveries', 'viewByUser'),
(987, '658a19ba1968b4f7e48ad83620b7ef27', 320, 'transfer_orders', 'viewByUser'),
(988, '83b17b14c681fd10f5a272560e9d4ae6', 321, 'inv_adjs', 'viewByUser');
INSERT INTO `module_details` (`id`, `sys_code`, `module_id`, `controllers`, `views`) VALUES
(1007, '6aa26f0a8fe08532106c38fc6498580c', 328, 'point_of_sales', 'reprintReceiptSm'),
(1008, '94d7632d6e3e618bf0bd618f36cced6b', 149, 'point_of_sales', 'viewPosDaily'),
(1009, '683af9941b11c9d0e4002c56ea27d7e9', 149, 'point_of_sales', 'customer'),
(1010, '7379cf52512c566a5818f0f9d1b80163', 149, 'point_of_sales', 'customerAjax'),
(1011, 'f5bf7f3e60906036f50bff43e265cdbb', 149, 'point_of_sales', 'discount'),
(1012, 'd625f358efbe275768d4db9618a2a847', 224, 'ar_aging_employees', 'employee'),
(1013, '3a782299db3a88f5c3cbb06290c39d05', 224, 'ar_aging_employees', 'employeeAjax'),
(1019, 'd375d30c6f5592fd39e243eb4300bbb6', 334, 'shipments', 'index'),
(1020, '7b9703803b5e53836f78266d00d44d58', 334, 'shipments', 'ajax'),
(1021, '98a51f73b635ec4008533bd375589724', 334, 'shipments', 'view'),
(1022, '47a8d282efa016af3be8756f8c4e9b19', 335, 'shipments', 'add'),
(1023, 'ecf1f94a9c1db6f3806eeec8854430a3', 336, 'shipments', 'edit'),
(1024, '097a1305be2919938079b036fb72cf51', 337, 'shipments', 'delete'),
(1025, '2538becab7ebe0f1d9b2c4418e2ee029', 338, 'streets', 'index'),
(1026, '7a595cd13505d2ffe1fb3b0fb4dddb03', 338, 'streets', 'ajax'),
(1027, '43b9951555abd503dcbed7ba6bb6fc5f', 338, 'streets', 'view'),
(1028, 'e4386ea58fa35ee3ead9bcd2b5815035', 339, 'streets', 'add'),
(1029, '10e6e5ba31367c56910f2989cc969ed3', 340, 'streets', 'edit'),
(1030, '241bb66893ac0585f145b3f6bcb2e558', 341, 'streets', 'delete'),
(1031, '2213ed5919ee1811ba3f6afbfc886bd9', 342, 'reasons', 'index'),
(1032, '5d374d54703c7b16fb502b846e1b291f', 342, 'reasons', 'ajax'),
(1033, 'd90931af36117c6438783edf06d256b3', 342, 'reasons', 'view'),
(1034, 'fce13f1db075a1715713acc4f48d8f8e', 343, 'reasons', 'add'),
(1035, '866c61cb6bd099bbfd95e2c1fff88935', 344, 'reasons', 'edit'),
(1036, '4b4677fc67fcbddcd0edcf99daa1d2a6', 345, 'reasons', 'delete'),
(1037, '44450c5d3f91340703da3a5dfdd248e8', 346, 'places', 'index'),
(1038, 'dfa92c875a522566bac36c11efbcfa24', 346, 'places', 'ajax'),
(1039, '140d53f8669d30881147ceaa22297081', 346, 'places', 'view'),
(1040, '930db840f19cda9c6d34c3618ba0799c', 347, 'places', 'add'),
(1041, 'c1665a2132d4f370ca974c29b0e848fe', 348, 'places', 'edit'),
(1042, '16cf2b4b0757da805242a297b80de4f0', 349, 'places', 'delete'),
(1043, '4b636795e101c7567f8bda600b6cb5c1', 350, 'positions', 'index'),
(1044, 'fd5b5b8e2a2cc1669a266f8b19496c9c', 350, 'positions', 'ajax'),
(1045, 'be7ee081582477d5723d305634fbfa44', 350, 'positions', 'view'),
(1046, '68cadc0b9d4ae43adeb1c7fa36783aa1', 351, 'positions', 'add'),
(1047, '31fb8d35b673164c18c1a50290c9509b', 352, 'positions', 'edit'),
(1048, '5851e76cacda8a30f9079575df58b0be', 353, 'positions', 'delete'),
(1079, '5f7d8dce595539e229b8289bfa9e4546', 374, 'quotations', 'index'),
(1080, 'd6d8096ceeaa1cced8ad938efd187e27', 374, 'quotations', 'ajax'),
(1081, '69abd4ad34a82816736d22c8e63f0aba', 374, 'quotations', 'view'),
(1082, '1e281f78cf9ba2c58175edf0cf16f845', 374, 'quotations', 'product'),
(1083, '2daf24516aebeb11dd6c52f4ed24506a', 374, 'quotations', 'productAjax'),
(1084, '4575b9e17725e228a93a048aeaa0758e', 374, 'quotations', 'customer'),
(1085, 'c62d0271469a8bb7fc15925352abf43a', 374, 'quotations', 'customerAjax'),
(1086, 'dfd2ef569a06597da7fd69c9e2fe3867', 374, 'quotations', 'searchCustomer'),
(1087, '69b7ac7fbdd62c1870f30185d6e0fec2', 374, 'quotations', 'searchProduct'),
(1088, 'b075eb153c6114dd94c5727c129fca17', 374, 'quotations', 'printInvoice'),
(1089, 'aa2a99486875a0aecbb6d729ee1ed490', 374, 'quotations', 'searchProductByCode'),
(1090, '5360ce8162215199589822396678b03b', 375, 'quotations', 'add'),
(1091, '1b1616e5b4ac650783c6febbead1ab2a', 375, 'quotations', 'orderDetails'),
(1092, '7a92ba3fdb88d5dd91452a2f91b475ae', 376, 'quotations', 'edit'),
(1093, '8f25f21c0cd309479d65481007a1d88a', 376, 'quotations', 'editDetails'),
(1094, 'a0f4ce36fca9f5c7ce0a0a1858e6823f', 377, 'quotations', 'delete'),
(1095, '97324937cde5a1fe3784ba9b8fe94235', 378, 'quotations', 'viewByUser'),
(1096, '0309274c31e6ca80cfaa299ad935096b', 379, 'quotations', 'open'),
(1097, 'ced2432174b1599a1a80b3f99d3b7d62', 380, 'quotations', 'close'),
(1098, 'f4b2170dd97320b0f0a49dcb3066ef8e', 381, 'orders', 'index'),
(1099, 'e309766b0ac33f58a7b1df1dee0ddbd8', 381, 'orders', 'ajax'),
(1100, 'a7819c1aa546fb119afb4990b76552c5', 381, 'orders', 'view'),
(1101, 'dfeb37a2498707ad03cc9feb3dcd7769', 381, 'orders', 'product'),
(1102, '88e6ab5bd0ba2d3556090beee8bd94f4', 381, 'orders', 'productAjax'),
(1103, 'a03c379e248d4037d988644d76bae20d', 381, 'orders', 'customer'),
(1104, 'e306d84d7c7373e244a86886424ff258', 381, 'orders', 'customerAjax'),
(1105, '0a6cb0fa84b00a3d8d823f60e2e0a819', 381, 'orders', 'searchCustomer'),
(1106, '1a5fafa0f259d70b7342be0dd23c758d', 381, 'orders', 'searchProduct'),
(1107, '0984064ed53cdbbfffb39b9ab30f58b0', 381, 'orders', 'printInvoice'),
(1108, '68d4f86d539d43e056a1784870ba50df', 381, 'orders', 'searchProductByCode'),
(1109, '9f465ccfb238401aeca5ad22f9231217', 382, 'orders', 'add'),
(1110, 'f30ae74bd08af0d0baa5d9137fd187f5', 382, 'orders', 'orderDetails'),
(1111, 'e30a70c08088f705f5c9d3832b587790', 383, 'orders', 'edit'),
(1112, '12b34b9145fff80010e0dedf814b0969', 383, 'orders', 'editDetails'),
(1113, '46d3a408780fa175c0efe2a8d7928571', 384, 'orders', 'delete'),
(1114, 'c6fd0cb7fc11cfad24050232d4167dc3', 385, 'orders', 'viewByUser'),
(1115, '37eec224ad9f9569ebbd1e8beafe5809', 386, 'orders', 'open'),
(1116, '7009491c010893a9c7a04fde58dc7015', 387, 'orders', 'close'),
(1136, '25acfee64a070bdf8b5c736300db6b93', 381, 'orders', 'getProductFromQuote'),
(1137, 'cb39f2f58aa2d2d6a2362fc02c2be0ca', 246, 'inv_adjs', 'searchProductSku'),
(1138, 'dbfed884a1b7d69d95c9a22278ebe726', 246, 'inv_adjs', 'searchProductPuc'),
(1139, '7f605aaaf942fc606ee0ac61e8f22f4b', 246, 'inv_adjs', 'product'),
(1140, 'ce7e6a8c84b3c324fa47b1f6e89fa3de', 246, 'inv_adjs', 'productAjax'),
(1141, 'a375437ea03d2f10f9d99023864eb2ae', 246, 'inv_adjs', 'getTotalQtyOnHand'),
(1142, '5d8d328d1e27e213b3269a1d46b960be', 246, 'inv_adjs', 'searchProduct'),
(1143, '150d04480abf2d73975950e456155ded', 388, 'request_stocks', 'index'),
(1144, 'ed530c0c88648282e77e73525c37f7b8', 388, 'request_stocks', 'ajax'),
(1145, '07a0f1941b7966a2aee49694776694ed', 388, 'request_stocks', 'view'),
(1146, '1daff3123a5260cdbfae604acf780ac6', 388, 'request_stocks', 'searchProductCode'),
(1147, '015808396da9a7e88b6c1f0fef9081c1', 388, 'request_stocks', 'searchProduct'),
(1148, '031b056cf6fa2e61604e254805ac8a33', 388, 'request_stocks', 'product'),
(1149, 'e1142904dfcafd4e1e034768944591de', 388, 'request_stocks', 'productAjax'),
(1150, '9e54f5c18f79fb89ecf208879d01fcf0', 389, 'request_stocks', 'add'),
(1151, '7eec0de2b934d85d1c8d5e65e698a03c', 390, 'request_stocks', 'edit'),
(1152, 'dd3e44475b12d539e8c96c2c500bb758', 391, 'request_stocks', 'delete'),
(1153, 'd0693c07a02e2bcf8063785bc115a43c', 392, 'request_stocks', 'printInvoice'),
(1154, '6596c8c274b99cc5287876f2b0a3507a', 393, 'request_stocks', 'viewByUser'),
(1155, '5252f93bf581e0cafe5040cfc8c8cf9e', 101, 'transfer_orders', 'printInvoice'),
(1156, '8041937fab9d03f5f4403cd11c783c8d', 101, 'transfer_orders', 'searchProductCode'),
(1157, '3eb00e7c63624e457a68eda31f084a48', 101, 'transfer_orders', 'getProductTotalQty'),
(1158, '88cd3bfe79da8f4c55b76e1195b4fc27', 101, 'transfer_orders', 'product'),
(1159, 'be41ae67058eaf8846caaeb61feb9e3a', 101, 'transfer_orders', 'productAjax'),
(1160, '996deb72faaecee74207212c0f77f3fa', 101, 'transfer_orders', 'requestStock'),
(1161, '8204d592c727982ef50adcd30dc5c7ed', 101, 'transfer_orders', 'requestStockAjax'),
(1162, 'a3b2783a943f2e279e14c5d83a5346b3', 101, 'transfer_orders', 'getProductFromRequest'),
(1163, '49faeb85641fc201de43be0592e71600', 101, 'transfer_orders', 'searchProduct'),
(1164, '6ca611c8e4b61f60ec955fff7bb9e318', 101, 'transfer_orders', 'searchRequestStockCode'),
(1165, 'a32d92be7f3c4c27470f395e809a7d18', 126, 'transfer_receives', 'printInvoice'),
(1166, '41fe7b5415f7d32e23d2bf7b1ef58cef', 125, 'transfer_receives', 'view'),
(1167, 'dde2d28ad734c30ded4c79bb42dc8f97', 105, 'sales_orders', 'searchProductByCode'),
(1168, 'c592b3a1c94dc34729585d8813a25317', 105, 'sales_orders', 'searchProduct'),
(1169, 'e8ae365ca28422679323b0a4bb93996b', 105, 'sales_orders', 'product'),
(1170, 'a519f2ad6196e51029d755ce3934be81', 105, 'sales_orders', 'product_ajax'),
(1171, '3233bdbf5d0f532774c440b002c3b458', 105, 'sales_orders', 'customer'),
(1172, '15514cafb1ae27ce04524641ff56c9bc', 105, 'sales_orders', 'customer_ajax'),
(1173, 'ebd04767992fbfebdb0a988b6dbf2c2b', 105, 'sales_orders', 'employee'),
(1174, '6841c45543fdd83be865b829369efad9', 105, 'sales_orders', 'employeeAjax'),
(1175, 'c9f8c255576aabdda1acf1ede75bb5a3', 105, 'sales_orders', 'quotation'),
(1176, 'c7adc3e9db799147e2f7797e76179264', 105, 'sales_orders', 'quotationAjax'),
(1177, 'a2772da0dc0c2120c2f0a957b4dfe4b3', 105, 'sales_orders', 'getProductFromQuote'),
(1178, '3fc2a7934bd262d6816a0ab0e25f81ef', 105, 'sales_orders', 'searchQuotationCode'),
(1179, '881118cdf16a87bc4df4b80418160d28', 105, 'sales_orders', 'searchQuotation'),
(1180, '67ed23aa8775928373063fb39dc11c04', 105, 'sales_orders', 'searchOrder'),
(1181, '4ae5a77c9250d041302e98ae2b93f273', 105, 'sales_orders', 'searchOrderCode'),
(1182, '743b56bacc2c543fa214d4f8922e35ec', 105, 'sales_orders', 'getProductFromOrder'),
(1183, '71a6d8faec1827c32ae25736f386d9e3', 106, 'sales_orders', 'orderDetails'),
(1184, 'f9612f8b2da985432035af5e9c89f5f4', 211, 'sales_orders', 'edit'),
(1185, '47f2650da808458c062a69ecffcb004c', 211, 'sales_orders', 'editDetail'),
(1186, '16fe32fecff4906b3bb4a09c64ea2e01', 107, 'sales_orders', 'aging'),
(1187, '7fa6da281d5a18e420dbe866e6f6b053', 107, 'sales_orders', 'voidReceipt'),
(1188, '8f81e9d05efc6c2650112436c4736dec', 107, 'sales_orders', 'printReceipt'),
(1189, '69bf94ab229083e2d6d644ad4686facd', 107, 'sales_orders', 'printReceiptCurrent'),
(1190, 'cd768d542fe3e652b266d9513b5033b4', 108, 'sales_orders', 'void'),
(1191, '44ff964d5f9c15f8c64c8ff29f5342a5', 147, 'sales_orders', 'miscellaneous'),
(1192, '278f04f0a577edc692f0390f6eeddc53', 148, 'sales_orders', 'service'),
(1193, '39f95aa740241c67215344875c77ea15', 181, 'sales_orders', 'discount'),
(1194, '532697f688474eb842978bcbd15126e1', 164, 'sales_orders', 'printInvoice'),
(1195, '5f0047779cbbbb96bdef03de50a3899f', 290, 'sales_orders', 'editUnitPrice'),
(1196, 'f4ea4da27e6d52182719fd0bafdd3400', 316, 'sales_orders', 'viewByUser'),
(1197, '854e85d0ec57ffbaa47e333a0b5e2335', 394, 'sales_orders', 'approveSale'),
(1198, '51a56fffba1bc23de083209be58022ea', 284, 'deliveries', 'pickSlip'),
(1199, '9570b84bf007ec6205c98bf65182a010', 284, 'deliveries', 'printInvoicePickSlip'),
(1200, 'b6a83f09f378893ea35d65d842b4a59e', 395, 'deliveries', 'printInvoicePickSlip'),
(1201, 'ab613349a6466ec8b49fe4435736be8b', 202, 'credit_memos', 'salesOrder'),
(1202, '4ff611529c60db41a630c2e06b44ce9d', 202, 'credit_memos', 'salesOrderAjax'),
(1203, '88818eb101621feb681a8db0d60e1b7a', 202, 'credit_memos', 'getProductFromSales'),
(1204, '41b5d54e78c9ad5a9e9d98af34e3c1db', 202, 'credit_memos', 'searchSalesInvoice'),
(1213, '718b4566ed40ec6310114fb94854af11', 1, 'customers', 'searchCustomer'),
(1214, '8d9eeb5be7327f01716392a8471d4747', 50, 'customers', 'getVillage'),
(1215, '2eeb791394ce24543eba2c60178bf12e', 50, 'customers', 'searchCustomer'),
(1216, 'e0110a9f92339bbabbb12d1fac2c6452', 50, 'customers', 'searchCustomerByCode'),
(1217, '05c54e9921f1c07029433cb21a2094a4', 50, 'customers', 'upload'),
(1218, '95e7053a8b45f52d275a632e0fc5d8a3', 50, 'customers', 'cropPhoto'),
(1219, 'b334f4c6bfcb8efbf84d7e3223b7f307', 50, 'customers', 'vendor'),
(1220, '242df2d542c0849a871b335979ae7463', 50, 'customers', 'vendorAjax'),
(1221, '21376fef0e8fc47d85c8ca3e2f424576', 306, 'customers', 'delete'),
(1222, 'a0e0598406e65f34c99433ffa59fabed', 329, 'purchase_requests', 'close'),
(1223, '149f27e78c50b5989b98f255b4823820', 397, 'purchase_requests', 'open'),
(1224, '9a34f8fc9ea7b30808d30888507e993c', 91, 'purchase_orders', 'product'),
(1225, '9fd45280a178be82c3d1458f449c8463', 91, 'purchase_orders', 'productAjax'),
(1226, '21645018565c0b23d0638ccfcd3137a4', 91, 'purchase_orders', 'vendor'),
(1227, '50b07e2d1427fa6eca6f8a88e1873e85', 91, 'purchase_orders', 'vendorAjax'),
(1228, '06ce82ca8850d723f7d05bc2f62fbdb6', 91, 'purchase_orders', 'searchProductCode'),
(1229, '31bf50de8e2be5aa3628ab6cb945ae7c', 91, 'purchase_orders', 'searchProduct'),
(1230, 'c1fe195c65cc5ac3fd952381b740516d', 91, 'purchase_orders', 'searchVendor'),
(1231, 'd24664da2e4062208c74f1e0c261b208', 91, 'purchase_orders', 'purchaseRequest'),
(1232, 'd2f0d66163ceb5a8e3cafa09ec6bdd9d', 91, 'purchase_orders', 'purchaseRequestAjax'),
(1233, 'f7a8001ea50548c22c3975a437aa6143', 91, 'purchase_orders', 'getProductsFromPO'),
(1234, '3ebf768d85c4c87f690256ba5cebc81c', 91, 'purchase_orders', 'printReceiptOne'),
(1235, 'c3ac2c83aaac7e56fe91ddfb55422316', 205, 'purchase_returns', 'searchProduct'),
(1236, 'a6b4839cd9e2d58c73e76082b86a5b86', 205, 'purchase_returns', 'searchProductByCode'),
(1237, '1e1fb12658a3dce87efb83e4e7194bd1', 205, 'purchase_returns', 'product'),
(1238, '9ea29fe1998dcc76da26014f57504388', 205, 'purchase_returns', 'product_ajax'),
(1239, '5526b0aaf95810b9ec12c69f1b33ae34', 205, 'purchase_returns', 'vendor'),
(1240, 'e38e96e61d7f5b0bd12c8af3b69763aa', 205, 'purchase_returns', 'vendorAjax'),
(1241, '8b6af57d8c0b9ceb6d25a664a67cf5f1', 72, 'vendors', 'searchVendor'),
(1242, '13dedf4c410ba4010545cb0a9997e734', 72, 'vendors', 'searchVendorByCode'),
(1243, '24d2992cd493bc6fff90209fc255e6f8', 72, 'vendors', 'upload'),
(1244, 'd285c9083a7c1909a10cf04cccc315ac', 72, 'vendors', 'cropPhoto'),
(1245, 'ea7be4a357bfa8f66ac7103eb63c6b63', 72, 'vendors', 'address'),
(1246, '3f587ba4735262e0c87aa981939c5688', 282, 'deliveries', 'view'),
(1273, '31e188be38bbad5f4c5939ee72c0e3be', 247, 'employees', 'upload'),
(1274, 'b73f9ebb710f3b38261f7343fd02e1d0', 247, 'employees', 'cropPhoto'),
(1275, '61d0d625b4c84b3eddc6461fc62d1edb', 416, 'employees', 'status'),
(1276, '32fe3243b5425e853844ed751e62c9ab', 381, 'orders', 'quotation'),
(1277, 'c8099aacc116a36f15ce212f38a9c47f', 381, 'orders', 'quotationAjax'),
(1278, '995f6a0b2d3e51bc7222dc7cddca535b', 417, 'quotations', 'service'),
(1279, 'c73db5fcec7bd3a3c82e6bae2a181790', 374, 'quotations', 'serviceCombo'),
(1280, 'b5d47a9e0d2d52b941ddb7d071573d07', 418, 'quotations', 'miscellaneous'),
(1281, 'acf57ea759e393ed74e9cf3f5c0921e6', 419, 'orders', 'service'),
(1282, '054e909728dc7fed23e4f3b340d85d2d', 420, 'orders', 'miscellaneous'),
(1283, '1a792858d1a6a33f6baac7ea6866968a', 105, 'sales_orders', 'order'),
(1284, '2355f52bc5881fb35ff71d582e436c77', 105, 'sales_orders', 'orderAjax'),
(1287, '49255c37085eada64fb9963e2a9e3c2c', 421, 'quotations', 'editPrice'),
(1288, 'fd16cb8148dad9f51670d7623cffeea2', 422, 'orders', 'editPrice'),
(1289, '508b3e477ed54a3694e5fee4761ab265', 423, 'orders', 'discount'),
(1290, '7d54bcc87522d245393d00f7a9bfabc5', 424, 'quotations', 'discount'),
(1291, '7a207a8ef66611a87e8437b1a3e3a3df', 425, 'products', 'setCost'),
(1292, '5ba8049ca017519a81931cf1ef6d735b', 426, 'purchase_requests', 'editCost'),
(1293, '493c2ce5860d114c5e655f778f3002e0', 1, 'vendors', 'searchVendor'),
(1294, 'fe629853cf5bffac76f529b367816441', 1, 'cgroups', 'searchCustomer'),
(1295, 'f37cfe54a6220da576700fe1b6aebb54', 1, 'employees', 'searchEmployee'),
(1296, 'eac9b0459c34457a1ee8b46d250f244c', 101, 'transfer_orders', 'printInvoiceConsignment'),
(1297, 'be45492dfbe3b8a9c92c95965bb8b8d3', 427, 'vat_settings', 'index'),
(1298, '6aa07e0e853cfd113e4ff465dd6497b6', 427, 'vat_settings', 'ajax'),
(1299, '3809c7520f6a1e40ba59b889cd830488', 428, 'vat_settings', 'edit'),
(1300, 'beece124ec81afb1348e018e58e2a411', 1, 'products', 'setProductWithCustomer'),
(1301, 'd7bbe008fa21cbd32b6969f182776451', 128, 'companies', 'upload'),
(1302, '4d68a959fce2d7c1015153a15aa13770', 429, 'groups', 'add'),
(1303, '9b481f8fa66e666c91578bf672b4a3ae', 430, 'groups', 'edit'),
(1304, '4683932e14ba9ca1c1e34a38c6802f12', 431, 'groups', 'delete'),
(1305, '6248ee14b5ebdd4a4786556f3c73d006', 432, 'users', 'add'),
(1306, '9969db4320312b9439f067dfeb6ba94b', 433, 'users', 'edit'),
(1307, '9ba9073a34c2fa2f68c1a2fcf54f35c0', 434, 'users', 'delete'),
(1308, '33f7f18d8991dec3982c791fd05def49', 188, 'general_ledgers', 'searchPurchaseOrder'),
(1309, 'a358a9ff821a71c7e1292847852d9655', 188, 'general_ledgers', 'searchPurchaseBill'),
(1310, 'a066f83a5af3b875551c1856ef0a25dd', 188, 'general_ledgers', 'searchQuote'),
(1311, '4723a85f69975d5e5443969551893924', 188, 'general_ledgers', 'searchSalesInvoice'),
(1312, '0edffb900052502ffd27409921310994', 188, 'general_ledgers', 'purchaseRequest'),
(1313, 'a0bb4286b1b7b2606b8b4b25dbb6bf06', 188, 'general_ledgers', 'purchaseRequestAjax'),
(1314, '29b762e7bfbf5ac664728623328121f9', 188, 'general_ledgers', 'purchaseBill'),
(1315, '6bdc540118b78d93496b01fb82cef98f', 188, 'general_ledgers', 'purchaseBillAjax'),
(1316, '723a41f995b77dfe36b23b4a0db28bd5', 188, 'general_ledgers', 'quotation'),
(1317, 'a3dee5a6f6521bf8efac7fe010aac097', 188, 'general_ledgers', 'quotationAjax'),
(1318, 'bec704f55976e696a5f34d4d9601b0a3', 188, 'general_ledgers', 'salesInvoice'),
(1319, '4a22171d302deed9c3eea7628124d1c2', 188, 'general_ledgers', 'salesInvoiceAjax'),
(1320, 'b0964231410003feac5e91ded01542c4', 188, 'general_ledgers', 'editMakeDeposit'),
(1321, 'd41a18f5835f8a699a1952ea229493f2', 205, 'purchase_returns', 'searchVendor'),
(1322, '78a81602617dbea08f68a7f322a39625', 1, 'users', 'createSysAct'),
(1323, '73d8da3de1115ade9985db4890f26b0d', 435, 'term_conditions', 'index'),
(1324, '001d4a9be20a9f1805025974c7ac8679', 435, 'term_conditions', 'ajax'),
(1325, '938e03b26f84f456546b29c75d01036b', 435, 'term_conditions', 'view'),
(1326, 'aaf75d2822c5dbc39ec5ff3519e2f030', 436, 'term_conditions', 'add'),
(1327, '0e1f1f66144a7a3dc818bd97d406ed61', 437, 'term_conditions', 'edit'),
(1328, 'd57d9022d9adc9927236480919967959', 438, 'term_conditions', 'delete'),
(1329, 'a6ec1fae7eabb5a1f4364db65471a18d', 439, 'term_condition_types', 'index'),
(1330, '6752c753ce36c5ebf48abdf1cff7787c', 439, 'term_condition_types', 'ajax'),
(1331, '720cdf5deb7aa0f3398c8631ce3d95c7', 439, 'term_condition_types', 'view'),
(1332, '88e818e521499f80d3eb76416c604d61', 440, 'term_condition_types', 'add'),
(1333, '897d8094a77f6b9d727eff261ccfb54d', 441, 'term_condition_types', 'edit'),
(1334, 'b5d972fb244a79fd63221391364a990b', 442, 'term_condition_types', 'delete'),
(1335, 'a7582f8bfe0669908545b8ec7e62bcd3', 443, 'term_condition_applies', 'index'),
(1336, '9b79ba391a580c933ae645753c41d0c7', 443, 'term_condition_applies', 'ajax'),
(1337, 'bf0bdce0c232700e83f8f194a19c0061', 443, 'term_condition_applies', 'view'),
(1338, '656457fb25b82d2a9bed331840bd3fa4', 444, 'term_condition_applies', 'add'),
(1339, '5ee3e17d7500e261e98f18013529f601', 445, 'term_condition_applies', 'edit'),
(1340, 'f09d582692a34c4de6e9266ee9b72213', 446, 'term_condition_applies', 'delete'),
(1341, 'b3808643768f2200306513e232507101', 292, 'price_types', 'changeOrdering'),
(1342, 'a20a0289a881e4aad156ca4b543805bf', 447, 'customer_contacts', 'index'),
(1343, 'aea23b45eaf4e2771825602277fdf6bf', 447, 'customer_contacts', 'ajax'),
(1344, '4cab19431626c8fc5ae5231b3a947bc3', 447, 'customer_contacts', 'view'),
(1345, '02e119a6652c5842bd8c0e6fda586040', 448, 'customer_contacts', 'add'),
(1346, '3bd9445cd4ee010effb7ec6278eb6abc', 449, 'customer_contacts', 'edit'),
(1347, '72f8d2a0c1e18da727185fd84c47edfc', 450, 'customer_contacts', 'delete'),
(1348, 'b197dbb930af875afdf904f2350cdb64', 451, 'customer_contacts', 'exportExcel'),
(1349, '2ce4e6ac71faea73e44b023836fe465b', 452, 'quotations', 'editTermsCondition'),
(1350, '0e92edd6c4ca07e386a68beed9d24e86', 453, 'quotations', 'invoiceDiscount'),
(1351, '14fefecf44463c7fc318dcc4b587f2e3', 374, 'quotations', 'getCustomerContact'),
(1352, 'ce31bb513f125f9f9c0c548011228366', 374, 'quotations', 'searchCustomer'),
(1358, 'c8e9e196347db55dc55fc23be01d1dfb', 458, 'orders', 'editTermsCondition'),
(1359, '56abc3a0191ea482e51d2efa9d6a0a7d', 459, 'orders', 'invoiceDiscount'),
(1360, 'c51bee51d50d8d3ab9540800dc16a473', 381, 'orders', 'getCustomerContact'),
(1361, 'f1044e76c8e6db44f9d6abdbf3dcc557', 381, 'orders', 'searchCustomer'),
(1362, 'c6f657bd7cf22f343613219abc71072e', 460, 'purchase_requests', 'editTermsCondition'),
(1363, '357fc09a2f6a520c7e45fae43e117985', 461, 'sales_orders', 'invoiceDiscount'),
(1364, '4ae3626745ea7ab123fc68f58cf69666', 462, 'sales_orders', 'editTermsCondition'),
(1365, 'cfbfa6004d1678ea27f47b5ec0cabb2f', 463, 'vat_settings', 'add'),
(1366, '62a8aa53fab70622f44df08b632df5e7', 464, 'vat_settings', 'delete'),
(1367, 'da528f609a0a67d3577c3439e5dafe94', 105, 'sales_orders', 'getCustomerContact'),
(1368, '2163636302f6661e31b54f3b331ad6a9', 105, 'sales_orders', 'customerCondition'),
(1369, 'c800ee5b3d7b784807cafc8b99c90ed0', 202, 'credit_memos', 'invoiceDiscount'),
(1370, '1cd3bb1611106df83b70990f88e38775', 465, 'purchase_orders', 'service'),
(1371, 'a16516a6c8ab45e9e59faf4a0e6b84e1', 466, 'purchase_orders', 'invoiceDiscount'),
(1372, '05ac24c9104d7f40d0937c404a05d850', 1, 'dashboards', 'share'),
(1373, '1f0ef204dcbd44f40915bc20a9a51877', 1, 'dashboards', 'shareSave'),
(1374, '2042aa56e4a35eeeb7bf930dc0bcef90', 467, 'quotations', 'approve'),
(1375, '8ccb06d20ed1f74b92f5f1033847acd0', 374, 'quotations', 'saveShareQuote'),
(1376, 'b4ad2c7099455563431f3c005dd4ce55', 468, 'purchase_orders', 'close'),
(1377, '3d736b09e19815c9516aba6983d8f1c2', 469, 'currency_centers', 'index'),
(1378, 'e50180889d984d704b8c80264fcf2685', 469, 'currency_centers', 'ajax'),
(1379, '0ce42eced6d1f23ee440cfe25bb38487', 470, 'currency_centers', 'add'),
(1380, 'fdcb37b9a313df873492715cf7bc614d', 471, 'currency_centers', 'edit'),
(1381, '19d32165e539618c7e003ef82c50b12a', 472, 'currency_centers', 'delete'),
(1382, 'dc1e811d1f5c97389051123e542db35f', 473, 'branch_currencies', 'index'),
(1383, 'd3afb9d9647eb5e993eec73346c8ee7a', 473, 'branch_currencies', 'ajax'),
(1384, 'b25300bbebf93499f38750417b85ef61', 473, 'branch_currencies', 'add'),
(1385, '7dc75f4104c16520797217d56b4c8c91', 473, 'branch_currencies', 'edit'),
(1386, '56f2f33501019108ec006b11b604e3a8', 473, 'branch_currencies', 'delete'),
(1387, 'c0b3ea9b7786cb9b75e7ca06b20afb38', 477, 'branch_currencies', 'applyPos'),
(1388, 'aae4abcb2cb4c9211512ebdc9f5de652', 478, 'sales_targets', 'index'),
(1389, 'f2559c8fc7f5d36be954c8001dea361d', 478, 'sales_targets', 'ajax'),
(1390, '783e49b1acabe6f5aefb5a04b1462c99', 479, 'sales_targets', 'add'),
(1391, '1f9869c78cbfafa0c0c0bc7e96486a1c', 480, 'sales_targets', 'edit'),
(1392, '9e5a57324cf4663839a89717a14bc1b7', 481, 'sales_targets', 'delete'),
(1393, '2b77d9008d54bf8b87d8b143ce1fd8ff', 482, 'sales_targets', 'approve'),
(1394, '5393ff59558f4aa1c739ba60d6c0f6b1', 478, 'sales_targets', 'employee'),
(1395, '1d007ac400893cd40b3ed5353b8a9270', 478, 'sales_targets', 'employeeAjax'),
(1396, '511c0c2c09189f721aca94f744d305fe', 478, 'sales_targets', 'view'),
(1397, 'eda70aac750964f527d2d3e9d33be0e4', 483, 'orders', 'approve'),
(1398, 'be865e652e28142a4f79c7349214df42', 374, 'quotations', 'history'),
(1399, 'b948d53ffdaf5d4fe20208eb3c34f92d', 47, 'products', 'cloneProductInfo'),
(1400, '1a781bb1b44daa66395835bb6982de3d', 1, 'reports', 'searchCgroup'),
(1401, 'e76caa8c58402d93b094d7545d1b5cf7', 1, 'reports', 'searchCustomer'),
(1402, '2458ad3c54acf365caf4df68ecfe03fb', 1, 'reports', 'searchPgroup'),
(1403, 'e4e24f8ce1a994b8987c72175b825dd3', 1, 'reports', 'searchProduct'),
(1404, 'f512911cf4ae7036b99a5a348d6120d6', 1, 'reports', 'searchEmployee'),
(1405, 'dd873d01d8516bd3bfef6749823514a5', 1, 'reports', 'searchEgroup'),
(1406, '4b41f6e151bc62f6afa65b12a5099cae', 1, 'reports', 'searchVendor'),
(1407, '2a877282706051fb9199fc77243d2238', 485, 'products', 'viewTotalCostPrice'),
(1409, '4c435cbd38db69083205662141246f0e', 486, 'products', 'viewChangeCost'),
(1410, 'f16e2cafbf135f49153e45a9f1731837', 486, 'products', 'resultChangeCost'),
(1411, 'bcada26935b9234d6dfead4abd8af366', 487, 'request_stocks', 'viewRequestIssued'),
(1414, 'daeba29e68bc5bcea7f95afdb67a1ef0', 1, 'dashboards', 'userDashboard'),
(1415, '7def3ee8302b8aea71c1265d298d5cac', 489, 'reports', 'salesTopItem'),
(1416, '332784fbce3d29acb20b5dd53610047f', 489, 'reports', 'salesTopItemResult'),
(1417, '332f13379006fac6361b1118d76d5497', 489, 'reports', 'salesTopItemGraph'),
(1418, 'e3a5e75a22c4afe8f39cb084be372cf4', 490, 'products', 'viewCost'),
(1419, '0ba18bfc69dec631730d1f27a6ef0f37', 491, 'reports', 'salesTopCustomer'),
(1420, '4788305feeb03c1de696cb26e288fae8', 491, 'reports', 'salesTopCustomerResult'),
(1421, 'e69876acb07f8411eea42bc94206b2ce', 491, 'reports', 'salesTopCustomerGraph'),
(1422, '8355c66304a38bab68d8cfdb44ba28fe', 106, 'sales_orders', 'add'),
(1423, '2e0595da5e0727e9ae0dc5c43d93e1e5', 492, 'reports', 'salesBySalesman'),
(1424, '6a8462e44ef68d746b514689a228f27b', 492, 'reports', 'salesBySalesmanDetailResult'),
(1425, 'ebcddf8af7cb8808e0e5a987d28ef869', 493, 'reports', 'invoiceByRep'),
(1426, 'd7c22c66f1fd731642a4595d27cdd72d', 493, 'reports', 'invoiceByRepResult'),
(1427, 'bf8959e95284728db86f4ed97db16687', 493, 'reports', 'invoiceByRepAjax'),
(1428, '5a9a6e81ca4b8aefd8d14f207573c98d', 494, 'reports', 'statement'),
(1429, 'f3d47743fda1c565d0e5b5681b0e3d6b', 494, 'reports', 'statementResult'),
(1430, 'a76e6947a622a329381916e95c5ab1b4', 495, 'reports', 'statementByRep'),
(1431, '8fb795d880b806a04cbae20cfcd8d3fb', 495, 'reports', 'statementByRepResult'),
(1432, 'adfe516636f8e215d06636c820ce20ae', 496, 'reports', 'customerBalanceByInvoice'),
(1433, '14cab9f74d9ceef6bc97171ad8dc6155', 496, 'reports', 'customerBalanceByInvoiceResult'),
(1434, '426615899518749b93db264ad767938a', 496, 'reports', 'customerBalanceByInvoiceAjax'),
(1435, '4f7f26277ecda05037c643244389d341', 497, 'reports', 'vendorBalanceByInvoice'),
(1436, 'ddd6e50de5c77056cc4f087000046d63', 497, 'reports', 'vendorBalanceByInvoiceResult'),
(1437, 'ba71d51ef1de05ac3cdf0236b75ad11e', 497, 'reports', 'vendorBalanceByInvoiceAjax'),
(1438, '241456dec328fc8ae321375d6aa1de5d', 498, 'reports', 'auditTrail'),
(1439, '32fb483edb5018afedd0f8a4f4d43d21', 498, 'reports', 'auditTrailResult'),
(1440, 'b819e9fb13a10e47625edbc207ac392a', 499, 'inv_adjs', 'viewAdjustmentIssued'),
(1441, '4a66381985c9e7fca1f1dd16a8e18db7', 500, 'transfer_orders', 'viewTransferIssued'),
(1442, '67f632fbe5e188937062ded5efd35099', 501, 'quotations', 'viewQuotationNoApprove'),
(1443, '7f05c76f1b21ccf046456c5ef8d805ad', 502, 'orders', 'viewOrderNoApprove'),
(1444, '1ae0e6d5da1da0771048667aae1ec3b3', 503, 'sales_orders', 'viewInvoiceNoDelivery'),
(1445, '182c4fff229a3d1cc41604258dfb99ff', 504, 'credit_memos', 'viewCreditMemoIssued'),
(1446, '2cf47554ca78ec68c6608c534e9fd54c', 505, 'purchase_orders', 'viewPurchaseIssued'),
(1447, '20299afe08c6a8ae3014e361e1817326', 97, 'purchase_receives', 'receiveDetail'),
(1448, 'f86e45deb3de9e487ac41afb09ad811f', 506, 'reports', 'quotation'),
(1449, '082238e9144664053c0e0349ec2e2eaf', 506, 'reports', 'quotationResult'),
(1450, '14643dfa7f891015e9ddb1a60973289c', 506, 'reports', 'quotationAjax'),
(1451, '9a5dde8472e8816715b94c2691f8f31f', 507, 'reports', 'customerQuotation'),
(1452, 'ba40ec95d8ad712039bd8b5bca0e4550', 507, 'reports', 'customerQuotationResult'),
(1453, '68ee7ef6d47a9e6ac157f44b1175d4d0', 507, 'reports', 'customerQuotationAjax'),
(1454, '22e2c119f3d8c60a5ae550b2ed6e946d', 508, 'reports', 'customerSaleOrder'),
(1455, '21b4212edc1dd7134e0d1e34b89b39a5', 508, 'reports', 'customerSaleOrderResult'),
(1456, '08e53b91a4de73c511b9216682f8b98c', 508, 'reports', 'customerSaleOrderAjax'),
(1457, '1e3baf4df632c916e4eae3fa933525e4', 508, 'reports', 'customerSaleOrderProductSummaryResult'),
(1458, 'cf2758f3e3d1f2fead4da9a8e8f4509d', 508, 'reports', 'customerSaleOrderProductSummaryAjax'),
(1459, 'bf3437c35dbf6199ae44a7a7b77db732', 508, 'reports', 'customerSaleOrderProductResult'),
(1460, '0b53f7c53e9bdd6c7a48a4a51ec52d01', 508, 'reports', 'customerSaleOrderProductAjax'),
(1461, '69cc5a56d9bee9f2051facc514b37112', 508, 'reports', 'customerQuotationProductSummaryResult'),
(1462, '0c55c83b8f952813f49403b836c0371b', 508, 'reports', 'customerQuotationProductSummaryAjax'),
(1463, '399fa394a8ce82f0afd48d2ccce73455', 508, 'reports', 'customerQuotationProductResult'),
(1464, 'bfaae190a1a8c6fff52bc2bbbbb86c70', 508, 'reports', 'customerQuotationProductAjax'),
(1465, '8a4007bb33935735dcba4d5c9ea0a205', 109, 'exchange_rates', 'view'),
(1466, '0d53f09bebbd87a869b9b2bc236dc6b9', 47, 'products', 'viewProductHistory'),
(1467, 'a1c8645ea7f660a8c217877fdc0596d8', 509, 'products', 'viewProductReorderLevel'),
(1468, '3f78337835a7b152796c45223d90de12', 509, 'products', 'viewProductReorderLevelAjax'),
(1469, '2effb62de8c7142040abb444d658abec', 510, 'products', 'viewProductExpireDate'),
(1470, 'f16c258f42048781f9009294ca8d2cca', 510, 'products', 'viewProductExpireDateAjax'),
(1471, '3ead3ccbf921eb0c2f7c6f38d1b704df', 511, 'branches', 'index'),
(1472, 'fbf177699035ce908513a4e7703811ae', 511, 'branches', 'ajax'),
(1473, 'b6de86c86487356a2635f10dfe7cf5de', 511, 'branches', 'view'),
(1474, '366874fdbaec754ac8620414120765d9', 512, 'branches', 'add'),
(1475, 'c5f89718275d203ba3d41115e09a4f2f', 513, 'branches', 'edit'),
(1476, 'ca63c8d5537dcbb0cf5c8032c517ffbf', 514, 'branches', 'delete'),
(1477, '370ffabe780353542395cdd6d5ada25b', 1, 'users', 'getBranchByCompany'),
(1478, '029d8a37e3fb4df9daa88e75d29dbfb0', 136, 'products', 'productPriceDetail'),
(1479, 'bd6a45f8d0f2d59523bcda81bcac4888', 374, 'quotations', 'productHistory'),
(1480, '44a94693927de811a47c6e59401e68d1', 374, 'quotations', 'productHistoryAjax'),
(1481, '63b8b1a53a46817356254ff4e2a99fbe', 381, 'orders', 'productHistory'),
(1482, '7205022f056bede9dc38167681cc3b92', 381, 'orders', 'productHistoryAjax'),
(1483, '7e597692ecb798655e4af9c5a90db738', 105, 'sales_orders', 'productHistory'),
(1484, '31fab02d835d5d3a61a4ec2953623f55', 105, 'sales_orders', 'productHistoryAjax'),
(1485, '237d49800ec83c48673be8e5073940ed', 253, 'purchase_requests', 'productHistory'),
(1486, '6405a97f3c969edfb4d29b62f77c3700', 253, 'purchase_requests', 'productHistoryAjax'),
(1487, 'e540453cc47a741135011cce4e901d96', 91, 'purchase_orders', 'productHistory'),
(1488, 'fa95c40278273fc9981022bce235f4e1', 91, 'purchase_orders', 'productHistoryAjax'),
(1489, '0003079847bab8bb7de962b1c9c0c08f', 46, 'products', 'printProduct'),
(1490, '26ab738de6ed5486d098cdf447922816', 46, 'products', 'printProductByOne'),
(1491, '403bb6bb6f6654ee638629275a5281ce', 46, 'products', 'printBarcode'),
(1492, '303de3df893a3ae34718612e9bc8c4a1', 46, 'products', 'printBarcodeByOne'),
(1493, 'e49c72331494e63c72e6a79243212d08', 46, 'products', 'printProductByCheck'),
(1494, 'f6b1eb85c5c2c445de9165cd5a624e61', 515, 'branch_types', 'index'),
(1495, '2777817b98185a394afcccba94e8be19', 515, 'branch_types', 'ajax'),
(1496, 'e7e0c986cccebad7cbe21a9314418579', 515, 'branch_types', 'view'),
(1497, 'd300cd8c3192a3242b18ad2f65bb2fbc', 516, 'branch_types', 'add'),
(1498, 'd134d604979339f1cb6ee8ff73915af2', 517, 'branch_types', 'edit'),
(1499, '0fc51cb6de0ee096bda2f6bfe77e3865', 518, 'branch_types', 'delete'),
(1500, 'e853764dbf25967af21b445100a36fdd', 14, 'location_groups', 'searchCustomer'),
(1501, 'fa8fb45ce564348bab1a150ec6954b27', 14, 'location_groups', 'customer'),
(1502, 'ed3ca9d77f850dd9d9090e89e7d3ed6b', 14, 'location_groups', 'customerAjax'),
(1503, '278c16f7d76c0dae159f0893ce0e78d7', 519, 'consignments', 'index'),
(1504, 'c8e8611d51d669c13a1c2ac04296b9d0', 519, 'consignments', 'ajax'),
(1505, '095e73c81830b83c5a7932e389ddc779', 519, 'consignments', 'view'),
(1506, '674888b5bbde8ea489856478477bb18a', 520, 'consignments', 'add'),
(1507, 'feb33ab285e83cc1d88f02d9f4fc7d0a', 521, 'consignments', 'edit'),
(1508, '2c6eeb4283d1886a4211e91b51c4e5f3', 522, 'consignments', 'void'),
(1509, '0be66f3f4ef397c532ac1be36134e5ce', 523, 'consignments', 'receive'),
(1510, '68e9f971c9c67149324f0103c6321516', 524, 'consignments', 'viewByUser'),
(1511, '327cce4da7bd7140d642eec681cbde68', 525, 'consignments', 'printInvoice'),
(1512, '220bc15beaa183b85a5efaac3f674cb5', 519, 'consignments', 'product'),
(1513, '75c7c165caefcbbaafdb3a21fde45d1d', 519, 'consignments', 'product_ajax'),
(1514, '3bf5edcd1b95c8fd6b5160c8b9ea8f05', 519, 'consignments', 'customer'),
(1515, 'f7153689638bb0305f9267cf1c120d54', 519, 'consignments', 'customer_ajax'),
(1516, '5b7ceee6ab92eae31486dd5d8ea9014c', 519, 'consignments', 'employee'),
(1517, 'fac403b3f43b8694d07123a16ef24066', 519, 'consignments', 'employeeAjax'),
(1518, '862957d3ae7007b05bec9a5d061bcf2e', 519, 'consignments', 'searchProduct'),
(1519, 'addb20462567c7252b63544fc2bc6ae3', 519, 'consignments', 'searchProductByCode'),
(1520, '9d3d3fce016806b3cf9be2c70c891aaf', 519, 'consignments', 'getCustomerContact'),
(1521, '18998f3db6573a21277ee85c6da554f4', 520, 'consignments', 'orderDetails'),
(1522, 'c2fae4baff642e3fde848d7579de5270', 521, 'consignments', 'editDetail'),
(1523, 'bc12a8c4449ed7194d8ed33942bf29e5', 526, 'consignments', 'editTermsCondition'),
(1524, '22fcb3f88225fc780eea4d230e04ea41', 519, 'consignments', 'searchCustomer'),
(1525, '2fd06d6c5bb708db75344670800eef54', 527, 'consignment_returns', 'index'),
(1526, 'd227a8101443217ca0e73150010f2d7c', 527, 'consignment_returns', 'ajax'),
(1527, 'cfb1de0fdf272763a60f7754712045d8', 527, 'consignment_returns', 'view'),
(1528, '252477fce7a7169aab569e97e3475539', 527, 'consignment_returns', 'customer'),
(1529, '25b536285444f2c8d69306b9f649c8a3', 527, 'consignment_returns', 'customer_ajax'),
(1530, '5996d24fa559ab3dead7e0874e355c3d', 527, 'consignment_returns', 'searchCustomer'),
(1531, '4fa3a4a20746fdd3af06e8933a7047c8', 527, 'consignment_returns', 'getCustomerContact'),
(1532, '905bdc755bcd6c08cc6fdbd3bc8260e7', 527, 'consignment_returns', 'getConsignmentReturn'),
(1533, 'b4402ab4d35754102058cc04f83167d1', 528, 'consignment_returns', 'add'),
(1534, '43af830a102a8dbb2509fcda1df3503b', 528, 'consignment_returns', 'orderDetails'),
(1535, 'f99f73c79894ff4b9dacb95e7be422c8', 529, 'consignment_returns', 'edit'),
(1536, '2c6ce0dfd930b99a01f4f039f3cc9838', 529, 'consignment_returns', 'editDetail'),
(1537, 'ae0bd169d58c8d39d3e2066e0c73c9bb', 530, 'consignment_returns', 'void'),
(1538, 'b2029fb248740f3fe5a854872ede6ad5', 531, 'consignment_returns', 'printInvoice'),
(1539, '52a74c5fde74425cc24b78ded4690ac5', 532, 'consignment_returns', 'receive'),
(1540, '6c8cc2b684086539306ce77d011fc227', 533, 'consignment_returns', 'viewByUser'),
(1541, 'fb72402fe7b414afc48202ad45912920', 527, 'consignment_returns', 'consignment'),
(1542, 'ba17f362351d77cba38be302fd9f2502', 527, 'consignment_returns', 'consignmentAjax'),
(1543, 'abd760f5624b10a4e6b5b90e8b7dcdab', 534, 'vendor_consignments', 'index'),
(1544, '6699a1cff71a13516aea33f7066a51bd', 534, 'vendor_consignments', 'ajax'),
(1545, '21faca5eca50bd3366e7a5cc9682ab68', 534, 'vendor_consignments', 'view'),
(1546, 'de4bb17f9eb473b7477ff4667b701147', 534, 'vendor_consignments', 'vendor'),
(1547, '276103771099ddadc011dbfd607f0258', 534, 'vendor_consignments', 'vendorAjax'),
(1548, '2b8f87e331c658bac906c9e4c0c73592', 534, 'vendor_consignments', 'searchVendor'),
(1549, '52375cfc001af3804c28da58c3794720', 534, 'vendor_consignments', 'product'),
(1550, '516509e6add3a0051f3f369df151257b', 534, 'vendor_consignments', 'productAjax'),
(1551, 'a69d0c3fb33f951c85a2c52ea189abdb', 534, 'vendor_consignments', 'searchProduct'),
(1552, 'd456a2acfb0536e54461b3e726d4d73d', 534, 'vendor_consignments', 'searchProductCode'),
(1553, '4337792cf33e8354e7a528a080e56565', 535, 'vendor_consignments', 'add'),
(1554, '8c73f0a0d7f6a3ad5a753858de38ede4', 536, 'vendor_consignments', 'edit'),
(1555, '18ea81e19b5cfda2bafa778cbbb2295e', 537, 'vendor_consignments', 'delete'),
(1556, '6fab51a133e1912e0dc8873ce29e985a', 538, 'vendor_consignments', 'printInvoice'),
(1557, 'bbae0fecc30bf52398383c0ddc859920', 539, 'vendor_consignments', 'receive'),
(1558, 'cc87fe5d30e16ff2ba78186d7784148a', 540, 'vendor_consignments', 'viewByUser'),
(1559, 'eb52ecd8f26b812299258dc21cb2853a', 541, 'vendor_consignment_returns', 'index'),
(1560, 'fb03c9e47777ca715b6ca10c7ccc93cf', 541, 'vendor_consignment_returns', 'ajax'),
(1561, 'e309f01d43e647b9964d587221fa49ff', 541, 'vendor_consignment_returns', 'view'),
(1562, '19247298d92eccb8fe79c34a53e7f80f', 541, 'vendor_consignment_returns', 'vendor'),
(1563, '0439261dc1d2467e6cd367e732a4c300', 541, 'vendor_consignment_returns', 'vendorAjax'),
(1564, '769ac05193be18b1963baefbbe9088f7', 541, 'vendor_consignment_returns', 'searchVendor'),
(1566, '1c9b16d95c1b44e0bd627f9dc81c44dd', 541, 'vendor_consignment_returns', 'getVendorConsignmentReturn'),
(1567, 'b89da1919d8148275c55c02efbd46032', 541, 'vendor_consignment_returns', 'vendorConsignmentAjax'),
(1568, 'aa26d64b9be62187b2b6ed8b1caad410', 541, 'vendor_consignment_returns', 'vendorConsignment'),
(1569, '258a330f4fe70d967dc242d615388e9a', 542, 'vendor_consignment_returns', 'add'),
(1570, '5886d569c27fde8b9b7782d0e843ae98', 542, 'vendor_consignment_returns', 'orderDetails'),
(1571, 'd33e901d82a0edabe108aa7a8fc8f752', 543, 'vendor_consignment_returns', 'edit'),
(1572, '2f3b0f71ed1e48870cba93236795e014', 543, 'vendor_consignment_returns', 'editDetail'),
(1573, 'da4dd2bdef94f242ec1d8e140eeef6f4', 544, 'vendor_consignment_returns', 'void'),
(1574, '7985ebff64f1fccc32a2a8057ce9c982', 545, 'vendor_consignment_returns', 'printInvoice'),
(1575, 'a12fe72054991f8543a563871ebe2144', 546, 'vendor_consignment_returns', 'receive'),
(1576, 'c7837b4cbf43f7ff1da7d86400e5df52', 547, 'vendor_consignment_returns', 'viewByUser'),
(1577, '6932e8a37896aa6a26e53205c65f1aa0', 91, 'purchase_orders', 'vendorConsignment'),
(1578, 'edfb1795748729d352e2bd52355fa050', 91, 'purchase_orders', 'vendorConsignmentAjax'),
(1579, 'feffe8dbe5178399d10f4d45fa10c5c2', 91, 'purchase_orders', 'getVendorConsignment'),
(1580, 'a78e502313d8935e012f10500001ceb2', 548, 'landed_cost_types', 'index'),
(1581, '0d21e2a529a614e6d3b27063fd380a1c', 548, 'landed_cost_types', 'ajax'),
(1582, 'a9d15a7f9da69aeccfb7f84cb24970c7', 548, 'landed_cost_types', 'view'),
(1583, 'cef5ee1ba1862745af248a7434e78efb', 549, 'landed_cost_types', 'add'),
(1584, 'f99acada314be895f2dd27b44c9eb4a9', 550, 'landed_cost_types', 'edit'),
(1585, '106fc5c751eb48b4b6685e9428db3c38', 551, 'landed_cost_types', 'delete'),
(1586, '52202509b5ff182531a0d36a4b0117bb', 552, 'landing_costs', 'index'),
(1587, '8515a2b1702c7b6ed02dd5325ffd5259', 552, 'landing_costs', 'ajax'),
(1588, '07c72f5138970700ec22bc9fbebfd25d', 552, 'landing_costs', 'view'),
(1589, 'f0f22fda641fbe2397179cbc93e06a0b', 552, 'landing_costs', 'vendor'),
(1590, 'a4cb7ee0702486f72a4b0023d53c3853', 552, 'landing_costs', 'vendorAjax'),
(1591, '863eb36949e6b7b4fa6b25f5dedda27c', 552, 'landing_costs', 'searchVendor'),
(1592, '9dd63cbce36b9c0cfc8ec5872bdf281e', 552, 'landing_costs', 'purchaseBill'),
(1593, '490c3c078c18233150846ac90a76ac50', 552, 'landing_costs', 'purchaseBillAjax'),
(1594, '2dee1020c05fd807fb326091f844d903', 553, 'landing_costs', 'add'),
(1595, '47675ab7e324ec53f4f061b59a26e1b0', 553, 'landing_costs', 'orderDetails'),
(1596, 'd7da433399a5cf1a5da76e6ed15d1cf2', 554, 'landing_costs', 'edit'),
(1597, '106571d1d02f59beb1acfaab24cab71f', 554, 'landing_costs', 'editDetail'),
(1598, '160e4b9c4984e8ac05c19ff48378967b', 555, 'landing_costs', 'delete'),
(1599, 'ae0402ffb398f417fdde310896067dae', 556, 'landing_costs', 'aging'),
(1600, '59de725404529230d89bac1205b4c300', 557, 'landing_costs', 'viewByUser'),
(1601, '8ec0be4de7237c994587f17e8ff92631', 552, 'landing_costs', 'getPurchaseLandingCost'),
(1602, 'd0bb7efbf81ace467b9f1ad613d8fc31', 556, 'landing_costs', 'voidReceipt'),
(1603, '606585c2f907707889a16cf18c438688', 558, 'landing_costs', 'close'),
(1604, '6b19bf2f0a74de5f4a5056fcb83c7991', 559, 'location_group_types', 'index'),
(1605, 'bff87ed9f2e0eb383c8ec48bce010831', 559, 'location_group_types', 'ajax'),
(1606, '5ae20f68a8242affa974e738ae265744', 559, 'location_group_types', 'view'),
(1607, '1eba3243935072ed338ad2be6c11aa1b', 560, 'location_group_types', 'add'),
(1608, '80953a36513614639c4061f90a810bde', 561, 'location_group_types', 'edit'),
(1609, 'dac8bc321b9aead0cba81573a057a5d7', 562, 'location_group_types', 'delete'),
(1610, '4850ea9a6b6347d3eb5b911e6acf35ac', 563, 'location_group_types', 'exportExcel'),
(1611, 'aa9d109d5184005c5d4f195dc1514268', 564, 'locations', 'status'),
(1612, '2d5026cadcaca9205f87bfd4586b1fe1', 46, 'products', 'printByUomBarcode'),
(1613, 'c3a997a2b0d275795d65b49bac4d455f', 565, 'e_pgroup_shares', 'index'),
(1614, 'a7dc1261a596acf593d4ec1985918a05', 565, 'e_pgroup_shares', 'saveShopShare'),
(1615, '75e56d578aa02859f80c21197a995ff1', 565, 'e_pgroup_shares', 'editShopShare'),
(1616, '3614a4e407999c70ddb1f20e081f7a2b', 565, 'e_pgroup_shares', 'savePgroupShare'),
(1617, '3f3aa20c8a7da48658ebf39a6cdd7728', 565, 'e_product_shares', 'index'),
(1618, 'fbadd358a7a334f74c912e1dedaf8a57', 565, 'e_product_shares', 'ajax'),
(1619, 'a0dd24f8026c194f3fb1e581735b0b7e', 565, 'e_product_shares', 'productPrice'),
(1620, 'b60eb8d7832b72df54b6f0e0bb3d0575', 565, 'e_product_shares', 'productPriceDetail'),
(1621, '0fbd2d78443387badb709cf60c268de5', 566, 'sync_monitors', 'index'),
(1622, 'f944bf27cfb4007be3dc2be94261b39e', 566, 'sync_monitors', 'checkConnection'),
(1623, '6dc999a6128a8c6699e9be8866292e97', 566, 'sync_monitors', 'saveConfig'),
(1625, 'c4ead7c9d4e51cdb8c11deed0c0756b0', 567, 'products', 'viewActivityByGraph'),
(1626, 'b372fd86da9c676f745ba5862eea8f0d', 568, 'products', 'viewPurchaseSalesByGraph'),
(1627, 'c39284c592ad66e384460b41084a2139', 569, 'reports', 'inventoryConsignment'),
(1628, '0bac8411afbbf3d492d3f56d88306c4d', 569, 'reports', 'inventoryConsignmentParentResult'),
(1629, '00bb2bab65fedef86375698b296f92c9', 569, 'reports', 'inventoryConsignmentResult'),
(1630, 'c5c16df396ac2c996f8d89e643b8ef72', 569, 'reports', 'inventoryConsignmentWithGlobalResult'),
(1631, 'ca9ac57a25c396d03fbad06d0e121641', 569, 'reports', 'inventoryConsignmentWithGlobalDetailResult'),
(1632, '74a740226e12fe7cdd6d2f2fe77687ec', 569, 'reports', 'inventoryConsignmentDetailResult'),
(1633, 'bca764692e1262686b7882eec8d455d5', 570, 'reports', 'requestStock'),
(1634, 'bab5c54d8ced2ba164d2131c0e40b144', 570, 'reports', 'requestStockResult'),
(1635, '763e992d1e64c02680a1caaa7d80871e', 570, 'reports', 'requestStockAjax'),
(1636, '83c7db04f083443b6a4b58a6e19d42f0', 571, 'reports', 'requestByItem'),
(1637, '39e9ac515559d0bf74b51d69043915c4', 571, 'reports', 'requestByItemParentResult'),
(1638, '9f90e8e2b4a4666e891641c0561e3167', 571, 'reports', 'requestByItemResult'),
(1639, '2e31800d173d26501dd020663a9df0a8', 571, 'reports', 'requestByItemDetailResult'),
(1640, '71825fab9ad6d48244e9f69a7ca13908', 572, 'sales_consignments', 'index'),
(1641, '2a426fb6f19fa4438b5f5d6dd1230cdf', 572, 'sales_consignments', 'ajax'),
(1642, '2902c4b2f678b797071ead380a13bcee', 572, 'sales_consignments', 'view'),
(1643, '8e04f163b002a2373da3dafa0468aa56', 573, 'uoms', 'getRelativeUom'),
(1644, '76cd19df04270b27716ac916d655b5d7', 580, 'uoms', 'getRelativeUom'),
(1649, '8e982aec3bdc368c1c95c05f351c6f3a', 572, 'sales_consignments', 'customer'),
(1650, '247630826125a64826481ff53f5faf03', 572, 'sales_consignments', 'customer_ajax'),
(1651, '319ea9b05fbc56eaaf6480ac82e76ede', 572, 'sales_consignments', 'employee'),
(1652, '335a42818c18ae92001b45ee8604e769', 572, 'sales_consignments', 'employeeAjax'),
(1653, '2b9ade67999ee63a87a438babb7c236b', 573, 'sales_consignments', 'orderDetails'),
(1654, '829c04f043644242df8c91b4bff65134', 580, 'sales_consignments', 'edit'),
(1655, 'a17af172b4b1e8252b112415a278e11c', 580, 'sales_consignments', 'editDetail'),
(1656, 'dc833e6b120b124491b930e1bb3a4f8b', 574, 'sales_consignments', 'aging'),
(1657, '222e49575a6df7b8b3fe057114eee6da', 574, 'sales_consignments', 'voidReceipt'),
(1658, 'b9beaa059abb908dcc440015aa2e7b7c', 574, 'sales_consignments', 'printReceipt'),
(1659, '7b6b24721b675ee2ceba8622ecf728d1', 574, 'sales_consignments', 'printReceiptCurrent'),
(1660, '0152d6d10314898ea8209b124edee62c', 575, 'sales_consignments', 'void'),
(1661, '827aa59d000c7f35dc98eeb46723ca42', 576, 'sales_consignments', 'miscellaneous'),
(1662, '8d95ddbf9aa2d7368c56c174fc1f7a6e', 577, 'sales_consignments', 'service'),
(1663, 'fa08d0d14464e068fef118c89639ed78', 579, 'sales_consignments', 'discount'),
(1664, '03a2f81866e7484b4302de3d589095c2', 578, 'sales_consignments', 'printInvoice'),
(1665, '6eb4e483cd089c0129c175fabf5aea6b', 581, 'sales_consignments', 'editUnitPrice'),
(1666, 'e6f243ba6b15c4dedf30ae1a59c44508', 582, 'sales_consignments', 'viewByUser'),
(1667, 'd78bb5e179d14cf4de66a27edac3ec3d', 583, 'sales_consignments', 'pick'),
(1668, '846bbd792cce9b70e9ea9a4626bd2b40', 584, 'sales_consignments', 'invoiceDiscount'),
(1669, 'fb568f7d0e6c44398512ba0ce330351a', 585, 'sales_consignments', 'editTermsCondition'),
(1670, '9cd9dfeaef2904143c8480e6b625010f', 572, 'sales_consignments', 'getCustomerContact'),
(1671, 'cb19494a9b774ac0016d06c6e8c9b5f5', 572, 'sales_consignments', 'customerCondition'),
(1672, 'c3d632c46d574d8e0e5aa0a6cab8f4f6', 573, 'sales_consignments', 'add'),
(1673, 'fbe2a63ebaf4e52c4084c9087e60bc7d', 572, 'sales_consignments', 'productHistory'),
(1674, 'bd6ada0579a4b9ce0820393f8854abce', 572, 'sales_consignments', 'productHistoryAjax'),
(1675, '3481553d977a2973bcec7be14de1846f', 586, 'vendor_contacts', 'index'),
(1676, 'fbc325ee9399894e2544a1212a692212', 586, 'vendor_contacts', 'ajax'),
(1677, 'aff8324df626cc683e395d649042d365', 586, 'vendor_contacts', 'view'),
(1678, '549dcbb095041c5551cdd23271c239d8', 587, 'vendor_contacts', 'add'),
(1679, 'd3c5c5cb07de97c59c2fee330714dfd9', 588, 'vendor_contacts', 'edit'),
(1680, 'b70d0c8401e2344fdeda632f2316c265', 589, 'vendor_contacts', 'delete'),
(1681, '5b1f151c0eee67d4243ef52caba049db', 590, 'vendor_contacts', 'exportExcel'),
(1682, '3200f40fe83dc1a1421d283890272546', 1, 'dashboards', 'getProductCache'),
(1683, 'd4478f6eb3ae6c06fe8ffafc754ed968', 1, 'users', 'connection'),
(1684, '5f13f3f3aab28fa1e254758c6c9a67bf', 591, 'locations', 'viewProductLocation'),
(1685, '079dcc84652a8af02b6b615fdd7d1fd6', 591, 'locations', 'viewProductLocationAjax'),
(1686, '2ed5c15df5074a017ec26746d757e406', 592, 'location_groups', 'viewProductWarehouse'),
(1687, '203730ec46d930748b43211fd6d6f728', 592, 'location_groups', 'viewProductWarehouseAjax'),
(1688, '539e2b9c549b976468c6a01f75fdbaed', 594, 'warehouse_maps', 'index'),
(1689, '61ae67897432c8eac42350f2bccd01c9', 594, 'warehouse_maps', 'viewLocationDetail'),
(1690, '10790e865087e7bde8ad895f9e69f87a', 594, 'warehouse_maps', 'viewProductWarehouse'),
(1691, '388aae8680568e18388ef15072bcec88', 594, 'warehouse_maps', 'viewProductWarehouseAjax'),
(1692, '01e459615e12660bd2319a7bc6e7411b', 594, 'warehouse_maps', 'viewProductLocation'),
(1693, 'c885021c9c2ec7587a40a1e4df580a2a', 594, 'warehouse_maps', 'viewProductLocationAjax'),
(1694, 'b84973d6fd0446f0c6eaee7a8fb8fe76', 593, 'transfer_orders', 'approve'),
(1695, 'fac89a7e8548271640a7b4906fb393d8', 595, 'point_of_sales', 'quickAddCustomer'),
(1696, '94ae941e4be2438265a38d2c87ad8c3c', 596, 'point_of_sales', 'quickAddProduct'),
(1697, 'da6f9cea8bc852f42ee194365c4366f4', 597, 'general_settings', 'index'),
(1698, 'e9b80a4a0cf82ee211202106cb6a317e', 597, 'general_settings', 'save'),
(1699, '09e30768d554d396ca7ec07027f75a4b', 205, 'purchase_returns', 'purchaseBill'),
(1700, 'ae5d42c5d1f8e5d4506d9706e39cc328', 205, 'purchase_returns', 'purchaseBillAjax'),
(1701, 'd989a3ddb2576f90224dbcc75732febc', 149, 'point_of_sales', 'checkStartShift'),
(1702, 'c8ac06998f734403bbc6f9e498270f68', 149, 'point_of_sales', 'addShiftRegister'),
(1703, '6acabb0359b40dbca1a52742ef96f390', 149, 'point_of_sales', 'endShiftRegister'),
(1704, '56e57b7348756bb6bc5503470e26c8a1', 149, 'point_of_sales', 'printShift'),
(1705, '4667bd73463a72177f632932c9a1d49e', 149, 'point_of_sales', 'saveAdjShiftRegister'),
(1706, 'f2c127c64bc5ab5a206426ae6033f053', 149, 'point_of_sales', 'checkAdjShiftRegister'),
(1707, '5ec7e4177cdbd7bd54081dcb493fa3ef', 149, 'point_of_sales', 'getDataAdjShiftRegister'),
(1708, 'a6ec629b3998d5d94f9f34dc6e3a1332', 598, 'shifts', 'index'),
(1709, 'fe08a82f6d7115e94f1f50801dd1d798', 598, 'shifts', 'ajax'),
(1710, 'd815306ae9fc56479b4e1b7967bc3aec', 598, 'shifts', 'view'),
(1711, 'd4cea103afc6510afe825f20d3962bb7', 599, 'shift_collects', 'index'),
(1712, '89d3bdce4ac12f67a2df5a2c9c2ba3bc', 599, 'shift_collects', 'ajax'),
(1713, '5b336e9c3cdf4fba47d46e9a984d69c0', 599, 'shift_collects', 'view'),
(1714, '2af66946e5b4d402ee09acfd73ec0dd6', 599, 'shift_collects', 'save'),
(1715, '248b5d93c2ae96f75c4dee23d716c6ab', 599, 'shift_collects', 'printShiftCollect'),
(1716, '65df5c766bf7646ca6193834701f861d', 600, 'reports', 'posShiftControl'),
(1717, 'fed58089cedde6e964183a9221cf5f9a', 600, 'reports', 'posShiftControlAjax'),
(1718, 'f66a1bf2ac84e8dc1064f6369063cdde', 600, 'reports', 'posShiftControlResult'),
(1719, '93f5e4d2ca13eff6a3469f24266c28b5', 601, 'reports', 'posCollectShiftByUser'),
(1720, '57bcac11853717675a0038bd9b800ac1', 601, 'reports', 'posCollectShiftByUserAjax'),
(1721, 'e3afa95f97e27ccebea58e19cad29bee', 601, 'reports', 'posCollectShiftByUserResult'),
(1722, 'a45f9cbf0ca9672ae5710f489029f812', 572, 'sales_consignments', 'consignment'),
(1723, 'd8c884717c4e72d2d716fab5bd611be4', 572, 'sales_consignments', 'consignmentAjax'),
(1724, 'b5c26ada95b1668e6691359ed93188a5', 572, 'sales_consignments', 'getProductFromConsignment'),
(1725, '20897d7f69b617a6762f5edc48986f39', 602, 'dashboards', 'viewTotalSales'),
(1726, 'bbe4e0ed19155906d5132eedbbbe1038', 10, 'locations', 'printLayout'),
(1727, '05f388ae774a08c20aa07435100f19ad', 597, 'general_settings', 'saveRecalculate'),
(1728, 'f8460751e022755c69159b46c5dda6e2', 603, 'colors', 'index'),
(1729, 'aeb77dadd79bcfff978c3a3e846cceec', 603, 'colors', 'ajax'),
(1730, '44b3b5a05e973ea8d8fd37f28a98812c', 604, 'colors', 'add'),
(1731, 'fb05a67b50685c1f8e915ffd38d45ac9', 605, 'colors', 'edit'),
(1732, '8e49c3515eea8b8f2dfd2b27c575726a', 606, 'colors', 'delete'),
(1733, 'da95ff1ab4bcee6e12de9830bb20ec33', 523, 'consignments', 'pickProduct'),
(1734, '3edc6c659213dda408c48124feb0749d', 523, 'consignments', 'pickProductAjax'),
(1735, '28abec710f7c7c34ee7fdb4037a80816', 523, 'consignments', 'pickProductSave'),
(1736, '62761c3eeb3ee03516a0895950901bd6', 572, 'sales_consignments', 'searchCustomer'),
(1737, 'd75d81ddd0ba0bc6b9a81af0fa9026e2', 149, 'point_of_sales', 'getTotalQtyByLotExp'),
(1738, 'b27789fd304a8cfcf2278f5b2efbcb00', 607, 'expenses', 'index'),
(1739, '16add271afe36f969f15c3984e56d2af', 607, 'expenses', 'ajax'),
(1740, 'b516166b2cbb2e74fbb2b6129bdd41c2', 607, 'expenses', 'view'),
(1741, '052f478d3bfafe990c08566da7bc812f', 608, 'expenses', 'add'),
(1742, '4258fcd7e32f0f89f5b2ced282d26fb6', 609, 'expenses', 'edit'),
(1743, '37104a68ced0e74780ceeb33a3c9704f', 610, 'expenses', 'delete'),
(1744, '9148889d34b874561de85e20d432a1fc', 607, 'expenses', 'customer'),
(1745, '33b9bc18ebbe418fa63587ee7f26b33c', 607, 'expenses', 'customerAjax');
INSERT INTO `module_details` (`id`, `sys_code`, `module_id`, `controllers`, `views`) VALUES
(1746, 'bcecffc24216e5741015bc53516edf0f', 607, 'expenses', 'vendor'),
(1747, '897dcd3d427b8e2f11d0c0a23c1a4b21', 607, 'expenses', 'vendorAjax'),
(1748, '59b8ca8d5c5c1386fce13293b12995c5', 105, 'sales_orders', 'getProductByExp'),
(1749, '8bc6165ff510d1ff6ef47ab5f2405373', 205, 'purchase_returns', 'getProductByExp'),
(1750, '9883ecf0ba1dfb821377e9d8af8a239e', 61, 'products', 'addPgroup'),
(1751, '801cf45a4cd6a25b740000fe566341f5', 19, 'products', 'addUom'),
(1752, '775cda6d8fe50d96b54b71ca4c3866cd', 47, 'products', 'quickAdd'),
(1753, 'd91ffd30afcb018613877d3a414c0476', 194, 'vendors', 'addTerm'),
(1754, 'fbe6ab55163dc681c9aa72a89406648f', 287, 'vendors', 'addVgroup'),
(1755, '61fb013764739308f98179940654019a', 73, 'vendors', 'quickAdd'),
(1756, '12742c51afa0740d374f46c860e3c108', 43, 'customers', 'addCgroup'),
(1757, '40d17b79febc8ba58f9bb02d35e1d175', 43, 'customers', 'addTerm'),
(1758, '5e6855114f17e13c32e5a5f9298ba7e2', 51, 'customers', 'quickAdd'),
(1759, '12cf86bbf444187f8bacd1cc96555f19', 343, 'credit_memos', 'addReason'),
(1760, '9b6bb722bd2c36dcb4cb40ba26d4c6e4', 138, 'services', 'addSection'),
(1761, 'c8883f3bff8ab29dfd7a49cff52aaace', 611, 'dashboards', 'viewExpenseGraph'),
(1762, '83f33be179c0d22968105d6b353f0600', 612, 'dashboards', 'viewSalesTop10Graph'),
(1763, 'd510b1ca818f10472c517a9952ebeddf', 613, 'dashboards', 'viewProfitLoss'),
(1764, '845d16fede775990542f925c330316f0', 614, 'dashboards', 'viewReceivable'),
(1765, '13f0b3f5bbaf518181ec34f7ebb5b879', 615, 'dashboards', 'viewPayable'),
(1766, 'c298ae21be525a707dce66c126f47eae', 596, 'point_of_sales', 'addPgroup'),
(1767, '8aa4bb16e3881486add7a96fd4eebb02', 596, 'point_of_sales', 'addUom'),
(1768, '14c6b094cfc04ae07ddcc1f689641d8b', 596, 'point_of_sales', 'getSkuUom'),
(1769, 'ac6bdc87e0a0171fdf38471f822f55ea', 595, 'point_of_sales', 'addTerm'),
(1770, 'ddd7874345ec1ae16fa76b4267b094a7', 595, 'point_of_sales', 'addCgroup'),
(1771, '4b7582da21351c2029682f896c1773ec', 616, 'inventory_physicals', 'index'),
(1772, '0121d895215f95f0065d43f0ca84e427', 616, 'inventory_physicals', 'ajax'),
(1773, '62bb576308afd7b37d2cd4b56a0eb64b', 616, 'inventory_physicals', 'view'),
(1774, 'a2422ed9f13c2ae303945df260e9fe7f', 616, 'inventory_physicals', 'printInvoice'),
(1775, 'ae6742551c1306b2c16aae8aed940823', 617, 'inventory_physicals', 'add'),
(1776, '540d98f5817ff672e9bf6358ceda5c65', 617, 'inventory_physicals', 'addDetail'),
(1777, '68324977ce5f20059ab7b389af8fdc92', 618, 'inventory_physicals', 'edit'),
(1778, '54911aabec3e18ab3f44ab3de4a14208', 618, 'inventory_physicals', 'editDetail'),
(1779, '4bfe794d11722239d07db6f89735c2de', 616, 'inventory_physicals', 'uom'),
(1780, 'b5f6fde0470b734666d6dca7a9c872f9', 616, 'inventory_physicals', 'product'),
(1781, 'ac6be0685b4d7fdbba12737087226b62', 616, 'inventory_physicals', 'productAjax'),
(1782, 'c1a13e5e6d074088e634fa0dae1a7fe9', 616, 'inventory_physicals', 'searchProduct'),
(1783, '894de2b84fba51f0ca02c18013d24603', 619, 'inventory_physicals', 'delete'),
(1784, 'b0e32a0a8c7b6e9b000b966b421d42c3', 616, 'inventory_physicals', 'getTotalQtyOnHand'),
(1785, 'b637902327adb63d39e21528d54b0d77', 617, 'inventory_physicals', 'save'),
(1786, '99807616fe2a4327c4679824148491fb', 618, 'inventory_physicals', 'saveEdit'),
(1787, '0b9e6d070f50c9ab8bc67e5624fb44ed', 620, 'inventory_physicals', 'approve'),
(1788, 'b588d2b4de23a05faf16262e363b70bb', 616, 'inventory_physicals', 'searchProductPuc'),
(1789, 'a5ab428df9943bca2351b9636fa85db4', 616, 'inventory_physicals', 'searchProductSku'),
(1790, '000c8327fc4dcb3f97d61ec0f0ad2095', 621, 'point_of_sales', 'editPrice'),
(1792, '2d8a074217b8679539a8d56f67650421', 622, 'brands', 'index'),
(1793, '056832a6f134d2d2b9139f81651d13ff', 622, 'brands', 'ajax'),
(1794, 'ef7e45347809f3caa09154b33e6c1541', 622, 'brands', 'view'),
(1795, '9f43b8c180fce38e8b7a7fb8b363f221', 623, 'brands', 'add'),
(1796, '094f1ff93425b30c7d9d563dcbbb5e94', 623, 'products', 'addBrand'),
(1797, '4999953ffa3ca7154cbe45ec18f19586', 624, 'brands', 'edit'),
(1798, '3f37e3d99fa2dfcef9b580ad62a62de7', 624, 'brands', 'delete'),
(1799, '3426b8653969ef52ed9301094ac641dd', 164, 'sales_orders', 'printInvoiceNoHead'),
(1800, '93dfd8163d2f86b197568f8593ad0879', 46, 'products', 'viewProductInventory'),
(1801, '8e6cda3d2d8d8d70bc733d5fc417c86f', 149, 'point_of_sales', 'disByCard'),
(1802, '62bb576308afd7b37d2cd4b56a0eb64b', 616, 'users', 'checkInventoryPhysical'),
(1803, 'acb47f3e60906036f50bff43e265aa33', 280, 'point_of_sales', 'discountByItem'),
(1804, NULL, 626, 'patients', 'index'),
(1805, NULL, 626, 'patients', 'ajax'),
(1806, NULL, 626, 'patients', 'view'),
(1807, NULL, 626, 'patients', 'getPatient'),
(1808, NULL, 626, 'patients', 'printPatientForm'),
(1809, NULL, 626, 'patients', 'getFindPatient'),
(1810, NULL, 626, 'patients', 'getFindPatientAjax'),
(1811, NULL, 626, 'patients', 'addPatientWaitingNumber'),
(1812, NULL, 627, 'patients', 'add'),
(1813, NULL, 628, 'patients', 'edit'),
(1814, NULL, 629, 'patients', 'delete'),
(1815, NULL, 630, 'patients', 'returnPatient'),
(1816, NULL, 631, 'cashiers', 'checkout'),
(1817, NULL, 631, 'cashiers', 'getService'),
(1818, NULL, 631, 'cashiers', 'getServicePrice'),
(1819, NULL, 631, 'cashiers', 'checkoutDebt'),
(1820, NULL, 631, 'cashiers', 'patientPayment'),
(1821, NULL, 631, 'cashiers', 'printInvoiceReceipt'),
(1822, NULL, 631, 'cashiers', 'printInvoice'),
(1823, NULL, 631, 'cashiers', 'printInvoiceIpd'),
(1824, NULL, 631, 'cashiers', 'printInvoiceReceiptIpd'),
(1825, NULL, 631, 'cashiers', 'discount'),
(1826, NULL, 631, 'cashiers', 'getService'),
(1827, NULL, 631, 'cashiers', 'getServicePrice'),
(1828, NULL, 633, 'cashiers', 'printInvoiceVat'),
(1829, NULL, 632, 'cashiers', 'printInvoiceDetail'),
(1830, NULL, 634, 'cashiers', 'dashboard'),
(1831, NULL, 634, 'cashiers', 'dashboardPaymentAjax'),
(1832, NULL, 634, 'cashiers', 'cashierDebtAjax'),
(1833, NULL, 634, 'cashiers', 'cashierInvoiceAjax'),
(1834, NULL, 634, 'cashiers', 'printInvoiceReceipt'),
(1835, NULL, 634, 'cashiers', 'printInvoice'),
(1836, NULL, 634, 'cashiers', 'dashboardPatientIpdAjax'),
(1837, NULL, 634, 'cashiers', 'printPatientService'),
(1838, NULL, 635, 'doctors', 'dashboard'),
(1839, NULL, 635, 'doctors', 'dashboardPatientQueueAjax'),
(1840, NULL, 635, 'doctors', 'consultation'),
(1841, NULL, 636, 'doctors', 'tabConsult'),
(1842, NULL, 636, 'doctors', 'tabConsultNum'),
(1843, NULL, 637, 'labos', 'laboList'),
(1844, NULL, 637, 'labos', 'laboListAjax'),
(1845, NULL, 637, 'labos', 'printLabo'),
(1846, NULL, 637, 'labos', 'view'),
(1847, NULL, 637, 'labos', 'edit'),
(1848, NULL, 637, 'labos', 'viewAfterPrint'),
(1849, NULL, 637, 'labos', 'editAfterPrint'),
(1850, NULL, 637, 'labos', 'printLaboAfterPrint'),
(1851, NULL, 637, 'labos', 'updateLaboStatus'),
(1852, NULL, 637, 'labos', 'laboResultSave'),
(1853, NULL, 637, 'labos', 'savePrint'),
(1854, NULL, 637, 'labos', 'printLaboWithoutCategory'),
(1855, NULL, 637, 'labos', 'bloodTest'),
(1856, NULL, 637, 'labos', 'index'),
(1857, NULL, 637, 'labos', 'queueLabo'),
(1858, NULL, 637, 'labos', 'queueLaboAjax'),
(1859, NULL, 637, 'labos', 'queueLaboByPassDoctor'),
(1860, NULL, 637, 'labos', 'queueLaboByPassDoctorAjax'),
(1861, NULL, 637, 'labos', 'laboRequest'),
(1862, NULL, 637, 'labos', 'laboTestRequest'),
(1863, NULL, 637, 'labos', 'laboRequestSave'),
(1864, NULL, 638, 'labo_items', 'index'),
(1865, NULL, 638, 'labo_items', 'ajax'),
(1866, NULL, 639, 'labo_items', 'add'),
(1867, NULL, 640, 'labo_items', 'edit'),
(1868, NULL, 641, 'labo_items', 'delete'),
(1869, NULL, 642, 'labo_item_categories', 'index'),
(1870, NULL, 642, 'labo_item_categories', 'ajax'),
(1871, NULL, 642, 'labo_item_categories', 'ajaxValidateFieldUser'),
(1872, NULL, 643, 'labo_item_categories', 'add'),
(1873, NULL, 644, 'labo_item_categories', 'edit'),
(1874, NULL, 645, 'labo_item_categories', 'delete'),
(1903, NULL, 663, 'labo_item_groups', 'index'),
(1904, NULL, 663, 'labo_item_groups', 'ajax'),
(1905, NULL, 663, 'labo_item_groups', 'laboItemAjax'),
(1906, NULL, 663, 'labo_item_groups', 'view'),
(1907, NULL, 664, 'labo_item_groups', 'add'),
(1908, NULL, 665, 'labo_item_groups', 'edit'),
(1909, NULL, 666, 'labo_item_groups', 'delete'),
(1910, NULL, 667, 'labo_item_groups', 'insurance'),
(1911, NULL, 667, 'labo_item_groups', 'insuranceAjax'),
(1912, NULL, 667, 'labo_item_groups', 'viewInsurance'),
(1913, NULL, 667, 'labo_item_groups', 'deleteInsurance'),
(1914, NULL, 667, 'labo_item_groups', 'editInsurance'),
(1915, NULL, 667, 'labo_item_groups', 'addInsurance'),
(1916, NULL, 668, 'labo_item_groups', 'cloneServicePrice'),
(1917, NULL, 669, 'labo_item_groups', 'deleteServicePrice'),
(1918, NULL, 670, 'labo_item_groups', 'exportExcel'),
(1919, NULL, 671, 'labo_title_groups', 'index'),
(1920, NULL, 671, 'labo_title_groups', 'ajax'),
(1921, NULL, 671, 'labo_title_groups', 'laboItemAjax'),
(1922, NULL, 672, 'labo_title_groups', 'add'),
(1923, NULL, 673, 'labo_title_groups', 'edit'),
(1924, NULL, 674, 'labo_title_groups', 'delete'),
(1925, NULL, 675, 'labo_title_items', 'index'),
(1926, NULL, 675, 'labo_title_items', 'ajax'),
(1927, NULL, 676, 'labo_title_items', 'add'),
(1928, NULL, 677, 'labo_title_items', 'edit'),
(1929, NULL, 678, 'labo_title_items', 'delete'),
(1930, NULL, 679, 'labo_units', 'index'),
(1931, NULL, 679, 'labo_units', 'ajax'),
(1932, NULL, 679, 'labo_units', 'addAjax'),
(1933, NULL, 680, 'labo_units', 'add'),
(1934, NULL, 681, 'labo_units', 'edit'),
(1935, NULL, 682, 'labo_units', 'delete'),
(1936, NULL, 683, 'age_for_labos', 'index'),
(1937, NULL, 683, 'age_for_labos', 'ajax'),
(1938, NULL, 684, 'age_for_labos', 'add'),
(1939, NULL, 685, 'age_for_labos', 'edit'),
(1940, NULL, 686, 'age_for_labos', 'delete'),
(1941, NULL, 687, 'labo_medicines', 'index'),
(1942, NULL, 687, 'labo_medicines', 'ajax'),
(1943, NULL, 688, 'labo_medicines', 'add'),
(1944, NULL, 689, 'labo_medicines', 'edit'),
(1945, NULL, 690, 'labo_medicines', 'delete'),
(1946, NULL, 691, 'labo_sites', 'index'),
(1947, NULL, 691, 'labo_sites', 'ajax'),
(1948, NULL, 692, 'labo_sites', 'add'),
(1949, NULL, 693, 'labo_sites', 'edit'),
(1950, NULL, 694, 'labo_sites', 'delete'),
(1951, NULL, 695, 'labo_sub_title_groups', 'index'),
(1952, NULL, 695, 'labo_sub_title_groups', 'ajax'),
(1953, NULL, 695, 'labo_sub_title_groups', 'view'),
(1954, NULL, 696, 'labo_sub_title_groups', 'add'),
(1955, NULL, 697, 'labo_sub_title_groups', 'edit'),
(1956, NULL, 698, 'labo_sub_title_groups', 'delete'),
(1957, NULL, 699, 'labo_sub_title_groups', 'exportExcel'),
(1958, NULL, 635, 'doctors', 'tabLabo'),
(1959, NULL, 635, 'doctors', 'tabLaboNum'),
(1960, NULL, 635, 'doctors', 'tabPrescription'),
(1961, NULL, 635, 'doctors', 'tabPrescriptionNum'),
(1962, NULL, 635, 'doctors', 'tabOtherService'),
(1963, NULL, 635, 'doctors', 'tabOtherServiceNum'),
(1964, NULL, 635, 'doctors', 'laboRequestSave'),
(1965, NULL, 635, 'doctors', 'printLab'),
(1966, NULL, 635, 'doctors', 'viewLaboResult'),
(1967, 'dfeb37a2498707ad03cc9feb3dcd7769', 635, 'doctors', 'orderDetails'),
(1968, 'dfeb37a2498707ad03cc9feb3dcd7769', 635, 'doctors', 'product'),
(1969, NULL, 635, 'doctors', 'productAjax'),
(1970, NULL, 635, 'doctors', 'searchProduct'),
(1971, NULL, 635, 'doctors', 'searchProductByCode'),
(1972, NULL, 700, 'labos', 'queueLabo'),
(1973, NULL, 635, 'doctors', 'service'),
(1974, NULL, 635, 'doctors', 'miscellaneous'),
(1975, NULL, 701, 'dashboards', 'voidServiceInvoice'),
(1976, NULL, 702, 'dashboards', 'voidReceipt'),
(1977, NULL, 703, 'doctors', 'patient'),
(1978, NULL, 703, 'doctors', 'patientAjax'),
(1979, NULL, 703, 'doctors', 'view'),
(1980, NULL, 703, 'doctors', 'viewLaboResult'),
(1981, NULL, 703, 'doctors', 'tabConsultNum'),
(1982, NULL, 703, 'doctors', 'printRecord'),
(1983, NULL, 703, 'doctors', 'tabLaboNum'),
(1984, NULL, 703, 'doctors', 'printLab'),
(1985, NULL, 704, 'appointments', 'index'),
(1986, NULL, 704, 'appointments', 'ajax'),
(1987, NULL, 705, 'appointments', 'add'),
(1988, NULL, 706, 'appointments', 'edit'),
(1989, NULL, 707, 'appointments', 'delete'),
(1990, NULL, 707, 'appointments', 'cancelAppointment'),
(1991, NULL, 708, 'company_insurances', 'index'),
(1992, NULL, 708, 'company_insurances', 'ajax'),
(1993, NULL, 708, 'company_insurances', 'view'),
(1994, NULL, 709, 'company_insurances', 'add'),
(1995, NULL, 710, 'company_insurances', 'edit'),
(1996, NULL, 711, 'company_insurances', 'delete'),
(1997, NULL, 712, 'group_insurances', 'index'),
(1998, NULL, 712, 'group_insurances', 'ajax'),
(1999, NULL, 712, 'group_insurances', 'view'),
(2000, NULL, 713, 'group_insurances', 'add'),
(2001, NULL, 714, 'group_insurances', 'edit'),
(2002, NULL, 715, 'group_insurances', 'delete'),
(2003, NULL, 716, 'services_price_insurances', 'index'),
(2004, NULL, 716, 'services_price_insurances', 'ajax'),
(2005, NULL, 716, 'services_price_insurances', 'view'),
(2006, NULL, 717, 'services_price_insurances', 'add'),
(2007, NULL, 718, 'services_price_insurances', 'edit'),
(2008, NULL, 719, 'services_price_insurances', 'delete'),
(2009, NULL, 720, 'services_price_insurances', 'cloneServicePrice'),
(2010, NULL, 721, 'services_price_insurances', 'deleteServicePrice'),
(2011, NULL, 722, 'services_price_insurances', 'exportExcel'),
(2012, NULL, 635, 'doctors', 'printInvoice'),
(2013, NULL, 703, 'doctors', 'printInvoice'),
(2014, NULL, 626, 'patients', 'printDoctorWaiting'),
(2015, NULL, 723, 'reports', 'customerReceipt'),
(2016, NULL, 723, 'reports', 'customerReceiptResult'),
(2017, NULL, 723, 'reports', 'customerReceiptAjax'),
(2018, NULL, 723, 'reports', 'searchPatient'),
(2019, NULL, 724, 'reports', 'customerLabo'),
(2020, NULL, 724, 'reports', 'customerLaboResult'),
(2021, NULL, 724, 'reports', 'customerLaboAjax'),
(2022, NULL, 725, 'reports', 'sectionService'),
(2023, NULL, 725, 'reports', 'sectionServiceAjax'),
(2024, NULL, 725, 'reports', 'sectionServiceResult'),
(2025, NULL, 726, 'reports', 'customerClientInsurance'),
(2026, NULL, 726, 'reports', 'customerClientInsuranceResult'),
(2027, NULL, 726, 'reports', 'customerClientInsuranceAjax'),
(2028, NULL, 626, 'patients', 'searchPatient'),
(2029, NULL, 626, 'patients', 'quickAddPatient'),
(2030, NULL, 51, 'customers', 'quickAddPatient'),
(2031, NULL, 595, 'point_of_sales', 'quickAddPatient'),
(2032, NULL, 727, 'general_settings', 'followDoctor'),
(2033, NULL, 728, 'general_settings', 'followNurse'),
(2034, NULL, 729, 'general_settings', 'followLabo'),
(2039, NULL, 730, 'patient_vital_signs', 'dashboard'),
(2040, NULL, 730, 'patient_vital_signs', 'dashboardPatientQueueAjax'),
(2041, NULL, 730, 'patient_vital_signs', 'vitalSign'),
(2042, NULL, 731, 'patient_vital_signs', 'addVitalSign'),
(2043, NULL, 733, 'echography_infoms', 'ajax'),
(2044, NULL, 733, 'echography_infoms', 'index'),
(2045, NULL, 733, 'echography_infoms', 'view'),
(2046, NULL, 734, 'echography_infoms', 'add'),
(2047, NULL, 735, 'echography_infoms', 'edit'),
(2048, NULL, 736, 'echography_infoms', 'delete'),
(2049, NULL, 737, 'indications', 'ajax'),
(2050, NULL, 737, 'indications', 'index'),
(2051, NULL, 737, 'indications', 'view'),
(2052, NULL, 738, 'indications', 'add'),
(2053, NULL, 739, 'indications', 'edit'),
(2054, NULL, 741, 'echo_services', 'echoServiceDoctorAjax'),
(2055, NULL, 741, 'echo_services', 'echoServiceDoctor'),
(2056, NULL, 742, 'echo_services', 'index'),
(2057, NULL, 742, 'echo_services', 'ajax'),
(2058, NULL, 742, 'echo_services', 'view'),
(2059, NULL, 742, 'echo_services', 'deleteImage'),
(2060, NULL, 743, 'echo_services', 'edit'),
(2061, NULL, 744, 'echo_services', 'printEchoService'),
(2062, NULL, 745, 'echo_services', 'add'),
(2063, NULL, 746, 'echo_services', 'addEchoServiceDoctor'),
(2064, NULL, 748, 'echo_services', 'addEchoServiceCardiaqueDoctor'),
(2065, NULL, 748, 'echo_services', 'printEchoServiceCardia'),
(2066, NULL, 747, 'echo_services', 'addEchoServiceObstetniqueDoctor'),
(2067, NULL, 748, 'echo_services', 'addEchoServiceCardiaqueDoctor'),
(2068, NULL, 748, 'echo_services', 'printEchoServiceCardia'),
(2069, NULL, 749, 'echo_service_cardias', 'view'),
(2070, NULL, 749, 'echo_service_cardias', 'ajax'),
(2071, NULL, 749, 'echo_service_cardias', 'index'),
(2072, NULL, 750, 'echo_service_cardias', 'edit'),
(2073, NULL, 749, 'echo_service_cardias', 'deleteImage'),
(2074, NULL, 751, 'echo_service_cardias', 'printEchoServiceCardia'),
(2075, NULL, 752, 'echographie_patients', 'view'),
(2076, NULL, 752, 'echographie_patients', 'ajax'),
(2077, NULL, 752, 'echographie_patients', 'index'),
(2078, NULL, 753, 'echographie_patients', 'edit'),
(2079, NULL, 754, 'echographie_patients', 'printObstetniquePatient'),
(2080, NULL, 755, 'xray_services', 'xrayServiceDoctorAjax'),
(2081, NULL, 755, 'xray_services', 'xrayServiceDoctor'),
(2082, NULL, 755, 'xray_services', 'getExtension'),
(2083, NULL, 756, 'xray_services', 'index'),
(2084, NULL, 756, 'xray_services', 'ajax'),
(2085, NULL, 756, 'xray_services', 'view'),
(2086, NULL, 756, 'xray_services', 'deleteImage'),
(2087, NULL, 757, 'xray_services', 'edit'),
(2088, NULL, 758, 'xray_services', 'printXrayService'),
(2089, NULL, 759, 'xray_services', 'add'),
(2090, NULL, 760, 'xray_services', 'addXrayServiceDoctor'),
(2091, NULL, 765, 'mid_wife_services', 'add'),
(2092, NULL, 766, 'mid_wife_services', 'addMidWifeServiceDoctor'),
(2093, NULL, 767, 'mid_wife_services', 'checkUpPatient'),
(2094, NULL, 769, 'mid_wife_services', 'addEditMidWife'),
(2095, NULL, 770, 'mid_wife_services', 'addNewMidWife'),
(2096, NULL, 771, 'mid_wife_services', 'addMidWifeServiceDoctorDossierMedical'),
(2097, NULL, 772, 'mid_wife_services', 'addNewDossierMedical'),
(2098, NULL, 773, 'mid_wife_services', 'editDossierMedical'),
(2099, NULL, 771, 'mid_wife_services', 'addMidWifeServiceDoctorDossierMedicalIndex'),
(2100, NULL, 774, 'mid_wife_services', 'addNewTracking'),
(2101, NULL, 775, 'mid_wife_services', 'editTracking'),
(2102, NULL, 776, 'mid_wife_services', 'addNewAccouchement'),
(2103, NULL, 777, 'mid_wife_services', 'editAccouchement'),
(2104, NULL, 778, 'mid_wife_services', 'addNewDeliverance'),
(2105, NULL, 779, 'mid_wife_services', 'editDeliverance'),
(2106, NULL, 780, 'mid_wife_services', 'addNewAccouchementFirstTime'),
(2107, NULL, 781, 'mid_wife_services', 'editAccouchementFirstTime'),
(2108, NULL, 782, 'mid_wife_services', 'addNewAccouchementNextTime'),
(2109, NULL, 783, 'mid_wife_services', 'editAccouchementNextTime'),
(2110, NULL, 768, 'mid_wife_services', 'editCheckUpPatient'),
(2111, NULL, 761, 'mid_wife_services', 'midWifeServiceDoctorAjax'),
(2112, NULL, 761, 'mid_wife_services', 'midWifeServiceDoctor'),
(2113, NULL, 762, 'mid_wife_services', 'index'),
(2114, NULL, 762, 'mid_wife_services', 'ajax'),
(2115, NULL, 762, 'mid_wife_services', 'view'),
(2116, NULL, 763, 'mid_wife_services', 'edit'),
(2117, NULL, 764, 'mid_wife_services', 'printMidWifeService'),
(2118, NULL, 784, 'doctor_consultations', 'index'),
(2119, NULL, 784, 'doctor_consultations', 'ajax'),
(2120, NULL, 784, 'doctor_consultations', 'view'),
(2121, NULL, 785, 'doctor_consultations', 'add'),
(2122, NULL, 786, 'doctor_consultations', 'edit'),
(2123, NULL, 787, 'doctor_consultations', 'delete'),
(2124, NULL, 635, 'labos', 'getResultLabo'),
(2125, NULL, 635, 'doctors', 'followup'),
(2126, NULL, 635, 'doctors', 'addNewFollowUp'),
(2127, NULL, 788, 'appointments', 'dashboardAppointment'),
(2128, NULL, 788, 'appointments', 'dashboardAppointmentAjax'),
(2129, NULL, 636, 'doctors', 'editConsult'),
(2130, NULL, 789, 'chief_complains', 'index'),
(2131, NULL, 789, 'chief_complains', 'ajax'),
(2132, NULL, 789, 'chief_complains', 'view'),
(2133, NULL, 790, 'chief_complains', 'add'),
(2134, NULL, 791, 'chief_complains', 'edit'),
(2135, NULL, 792, 'chief_complains', 'delete'),
(2136, NULL, 793, 'diagnostics', 'index'),
(2137, NULL, 793, 'diagnostics', 'ajax'),
(2138, NULL, 793, 'diagnostics', 'view'),
(2139, NULL, 794, 'diagnostics', 'add'),
(2140, NULL, 795, 'diagnostics', 'edit'),
(2141, NULL, 796, 'diagnostics', 'delete'),
(2142, NULL, 797, 'examinations', 'index'),
(2143, NULL, 797, 'examinations', 'ajax'),
(2144, NULL, 797, 'examinations', 'view'),
(2145, NULL, 798, 'examinations', 'add'),
(2146, NULL, 799, 'examinations', 'edit'),
(2147, NULL, 800, 'examinations', 'delete'),
(2148, NULL, 636, 'doctors', 'getDiagnosticDescription'),
(2149, NULL, 636, 'doctors', 'getExaminationDescription'),
(2150, NULL, 636, 'doctors', 'getChiefComplainDescription'),
(2151, NULL, 636, 'doctors', 'tabConsultAndrology'),
(2152, NULL, 636, 'doctors', 'tabConsultAndrologyNum'),
(2153, NULL, 635, 'doctors', 'printOtherService'),
(2154, NULL, 433, 'users', 'upload'),
(2155, NULL, 433, 'users', 'cropPhoto'),
(2156, NULL, 730, 'dashboards', 'cancelDoctor'),
(2157, NULL, 635, 'doctors', 'editOtherService'),
(2158, NULL, 801, 'cystoscopy_services', 'cystoscopyServiceDoctor'),
(2159, NULL, 802, 'cystoscopy_services', 'index'),
(2160, NULL, 802, 'cystoscopy_services', 'ajax'),
(2161, NULL, 802, 'cystoscopy_services', 'view'),
(2162, NULL, 803, 'cystoscopy_services', 'edit'),
(2164, NULL, 805, 'cystoscopy_services', 'printCystoscopyService'),
(2165, NULL, 801, 'cystoscopy_services', 'cystoscopyServiceDoctorAjax'),
(2166, NULL, 804, 'cystoscopy_services', 'addCystoscopyServiceDoctor'),
(2167, NULL, 631, 'cashiers', 'voidPayment'),
(2168, NULL, 806, 'uroflowmetry_services', 'uroflowmetryServiceDoctor'),
(2169, NULL, 807, 'uroflowmetry_services', 'index'),
(2170, NULL, 807, 'uroflowmetry_services', 'ajax'),
(2171, NULL, 807, 'uroflowmetry_services', 'view'),
(2172, NULL, 808, 'uroflowmetry_services', 'edit'),
(2173, NULL, 810, 'uroflowmetry_services', 'printUroflowmetryService'),
(2174, NULL, 806, 'uroflowmetry_services', 'uroflowmetryServiceDoctorAjax'),
(2175, NULL, 809, 'uroflowmetry_services', 'addUroflowmetryServiceDoctor'),
(2176, NULL, 809, 'uroflowmetry_services', 'deleteImage'),
(2177, NULL, 636, 'doctors', 'tabService'),
(2178, NULL, 636, 'doctors', 'tabServiceNum'),
(2179, NULL, 636, 'doctors', 'getService'),
(2180, NULL, 636, 'doctors', 'getServicePrice'),
(2181, NULL, 636, 'doctors', 'editTmpService'),
(2182, NULL, 703, 'doctors', 'printMedicalCertificate'),
(2183, NULL, 635, 'doctors', 'printTreatmentSticker'),
(2184, NULL, 811, 'patient_ipds', 'index'),
(2185, NULL, 811, 'patient_ipds', 'ajax'),
(2186, NULL, 811, 'patient_ipds', 'view'),
(2187, NULL, 811, 'patient_ipds', 'printPatientIpd'),
(2188, NULL, 811, 'patient_ipds', 'getService'),
(2189, NULL, 811, 'patient_ipds', 'getServicePrice'),
(2190, NULL, 812, 'patient_ipds', 'add'),
(2191, NULL, 813, 'patient_ipds', 'edit'),
(2192, NULL, 814, 'patient_ipds', 'delete'),
(2193, NULL, 815, 'patient_ipds', 'medicalSurgery'),
(2194, NULL, 815, 'patient_ipds', 'medicalSurgeryAjax'),
(2195, NULL, 815, 'patient_ipds', 'viewMedicalSurgery'),
(2196, NULL, 815, 'patient_ipds', 'printPatientMedicalSurgery'),
(2197, NULL, 816, 'patient_ipds', 'addMedicalSurgery'),
(2198, NULL, 817, 'patient_ipds', 'editMedicalSurgery'),
(2199, NULL, 818, 'patient_ipds', 'deleteMedicalSurgery'),
(2200, NULL, 819, 'patient_ipds', 'addService'),
(2201, NULL, 820, 'patient_ipds', 'addServiceMedicalSurgery'),
(2202, NULL, 812, 'patient_ipds', 'patientLeave'),
(2203, NULL, 821, 'patient_emergencies', 'index'),
(2204, NULL, 821, 'patient_emergencies', 'ajax'),
(2205, NULL, 821, 'patient_emergencies', 'view'),
(2206, NULL, 821, 'patient_emergencies', 'printPatientEmergency'),
(2207, NULL, 822, 'patient_emergencies', 'add'),
(2208, NULL, 823, 'patient_emergencies', 'edit'),
(2209, NULL, 824, 'patient_emergencies', 'delete'),
(2210, NULL, 825, 'patient_emergencies', 'viewDetail'),
(2211, NULL, 825, 'patient_emergencies', 'tabObservation'),
(2212, NULL, 825, 'patient_emergencies', 'addObservation'),
(2213, NULL, 825, 'patient_emergencies', 'editObservation'),
(2214, NULL, 825, 'patient_emergencies', 'printObservationMedical'),
(2215, NULL, 825, 'patient_emergencies', 'deleteObservation'),
(2216, NULL, 825, 'patient_emergencies', 'tabEvolutionNum'),
(2217, NULL, 825, 'patient_emergencies', 'addEvolutionNum'),
(2218, NULL, 825, 'patient_emergencies', 'editEvolutionNum'),
(2219, NULL, 825, 'patient_emergencies', 'printEvolutionNum'),
(2220, NULL, 825, 'patient_emergencies', 'deleteEvolutionNum'),
(2221, NULL, 826, 'patient_ipd_certificates', 'index'),
(2222, NULL, 826, 'patient_ipd_certificates', 'ajax'),
(2223, NULL, 826, 'patient_ipd_certificates', 'view'),
(2224, NULL, 826, 'patient_ipd_certificates', 'searchPatient'),
(2225, NULL, 826, 'patient_ipd_certificates', 'getFindPatient'),
(2226, NULL, 826, 'patient_ipd_certificates', 'getFindPatientAjax'),
(2227, NULL, 826, 'patient_ipd_certificates', 'printPatientIpdCertificate'),
(2228, NULL, 827, 'patient_ipd_certificates', 'add'),
(2229, NULL, 828, 'patient_ipd_certificates', 'edit'),
(2230, NULL, 829, 'patient_ipd_certificates', 'delete'),
(2231, NULL, 830, 'rooms', 'index'),
(2232, NULL, 830, 'rooms', 'ajax'),
(2233, NULL, 830, 'rooms', 'view'),
(2234, NULL, 831, 'rooms', 'add'),
(2235, NULL, 832, 'rooms', 'edit'),
(2236, NULL, 833, 'rooms', 'delete'),
(2237, NULL, 834, 'labos', 'approve'),
(2238, NULL, 835, 'labos', 'disapprove'),
(2239, NULL, 836, 'medical_histories', 'index'),
(2240, NULL, 836, 'medical_histories', 'ajax'),
(2241, NULL, 836, 'medical_histories', 'view'),
(2242, NULL, 837, 'medical_histories', 'add'),
(2243, NULL, 838, 'medical_histories', 'edit'),
(2244, NULL, 839, 'medical_histories', 'delete'),
(2245, NULL, 636, 'doctors', 'getMedicalHistory'),
(2246, '', 626, 'patients', 'printPatientCard'),
(2247, NULL, 811, 'patient_ipds', 'tabConsultNum'),
(2248, NULL, 811, 'patient_ipds', 'followup'),
(2249, NULL, 811, 'patient_ipds', 'addNewFollowUp'),
(2250, NULL, 811, 'patient_ipds', 'getFollowUp'),
(2251, NULL, 811, 'patient_ipds', 'tabPrescription'),
(2252, NULL, 811, 'patient_ipds', 'orderDetails'),
(2253, NULL, 840, 'doctor_comments', 'index'),
(2254, NULL, 840, 'doctor_comments', 'ajax'),
(2255, NULL, 840, 'doctor_comments', 'view'),
(2256, NULL, 841, 'doctor_comments', 'add'),
(2257, NULL, 842, 'doctor_comments', 'edit'),
(2258, NULL, 843, 'doctor_comments', 'delete'),
(2259, NULL, 844, 'treatment_uses', 'index'),
(2260, NULL, 844, 'treatment_uses', 'ajax'),
(2261, NULL, 844, 'treatment_uses', 'view'),
(2262, NULL, 845, 'treatment_uses', 'add'),
(2263, NULL, 846, 'treatment_uses', 'edit'),
(2264, NULL, 847, 'treatment_uses', 'delete'),
(2265, NULL, 848, 'daily_clinical_reports', 'index'),
(2266, NULL, 848, 'daily_clinical_reports', 'ajax'),
(2267, NULL, 848, 'daily_clinical_reports', 'view'),
(2268, NULL, 849, 'daily_clinical_reports', 'add'),
(2269, NULL, 850, 'daily_clinical_reports', 'edit'),
(2270, NULL, 851, 'daily_clinical_reports', 'delete'),
(2271, NULL, 636, 'doctors', 'getDoctorCommentDescription'),
(2272, NULL, 636, 'doctors', 'getDailyClinicalReportDescription'),
(2273, NULL, 635, 'uoms', 'getRelativeUom'),
(2274, NULL, 703, 'doctors', 'followup'),
(2275, NULL, 703, 'doctors', 'addNewFollowUp'),
(2276, NULL, 703, 'doctors', 'getDailyClinicalReportDescription'),
(2277, NULL, 626, 'patients', 'opdList'),
(2278, NULL, 626, 'patients', 'opdListAjax'),
(2279, NULL, 484, 'users', 'clearSession'),
(2280, 'adb8f4ce2668246504b5d76ed0dd5105', 852, 'reports', 'caseExpense'),
(2281, 'adb8f4ce2668246504b5d76ed0dd5107', 852, 'reports', 'caseExpenseResult'),
(2282, NULL, 811, 'patient_ipds', 'doctorComment'),
(2283, NULL, 811, 'patient_ipds', 'addNewDoctorComment'),
(2284, NULL, 811, 'patient_ipds', 'getDoctorComment'),
(2285, NULL, 811, 'patient_ipds', 'doctorDaignostic'),
(2286, NULL, 811, 'patient_ipds', 'addNewDoctorDaignostic'),
(2287, NULL, 811, 'patient_ipds', 'getDoctorDaignostic'),
(2288, NULL, 811, 'patient_ipds', 'tabLabo'),
(2289, NULL, 811, 'patient_ipds', 'laboRequestSave'),
(2290, NULL, 811, 'patient_ipds', 'tabOtherService'),
(2291, NULL, 811, 'patient_ipds', 'vitalSign'),
(2292, NULL, 811, 'patient_ipds', 'addVitalSign'),
(2293, NULL, 811, 'patient_ipds', 'getVitalSign'),
(2294, NULL, 811, 'patient_ipds', 'chiefComplain'),
(2295, NULL, 811, 'patient_ipds', 'addNewChiefComplain'),
(2296, NULL, 811, 'patient_ipds', 'getChiefComplain'),
(2297, NULL, 811, 'patient_ipds', 'medicalHistory'),
(2298, NULL, 811, 'patient_ipds', 'addNewMedicalHistory'),
(2299, NULL, 811, 'patient_ipds', 'getMedicalHistory'),
(2300, NULL, 853, 'reports', 'convertInvoice'),
(2301, NULL, 637, 'labos', 'deletePdfFile'),
(2316, NULL, 856, 'referrals', 'index'),
(2317, NULL, 856, 'referrals', 'ajax'),
(2318, NULL, 854, 'referrals', 'add'),
(2319, NULL, 855, 'referrals', 'edit'),
(2320, NULL, 856, 'referrals', 'view'),
(2321, NULL, 857, 'referrals', 'print'),
(2322, NULL, 858, 'referrals', 'delete'),
(2323, NULL, 859, 'referrals', 'exportExcel'),
(2324, NULL, 861, 'reports', 'serviceReferral'),
(2325, NULL, 861, 'reports', 'serviceReferralResult'),
(2326, NULL, 861, 'reports', 'serviceReferralAjax'),
(2327, NULL, 811, 'patient_ipds', 'printPatientLeave'),
(2328, NULL, 862, 'purchase_orders', 'showUnitCost');

-- --------------------------------------------------------

--
-- Table structure for table `module_introduces`
--

CREATE TABLE IF NOT EXISTS `module_introduces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `controllers` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pages` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_en` mediumtext COLLATE utf8_unicode_ci,
  `description_kh` mediumtext COLLATE utf8_unicode_ci,
  `step` int(11) DEFAULT NULL,
  `element` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `searchs` (`controllers`,`pages`),
  KEY `filter` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_types`
--

CREATE TABLE IF NOT EXISTS `module_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0: Disabled; 1: Enable',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=158 ;

--
-- Dumping data for table `module_types`
--

INSERT INTO `module_types` (`id`, `sys_code`, `name`, `ordering`, `status`) VALUES
(1, 'cb996c86207a08b1a599e7b6f125414f', 'Home Page', 101, 1),
(2, 'a23612854862345fc7838cd39ac0a3e6', 'User', 102, 1),
(3, '359cc158d7a49a2b7f40e7ff4eb58445', 'Group', 103, 1),
(4, 'da7e27dcfbcd411e50df94df1a81087d', 'Location', 107, 1),
(5, '9a0406de6a08f981ff727d2f3cf2f38f', 'Warehouse', 106, 1),
(6, '93c965fefc3afd248e6887020c597111', 'UoM', 108, 1),
(7, '6034114007bca67be50b8831234d7479', 'UoM Conversion', 109, 1),
(8, '4dd3111c4a451c2f48ac9eb403112347', 'Province', 618, 0),
(9, 'c65a68ed3df60658cb3f828baf301b3a', 'District', 619, 0),
(10, '44ec89f432ce5b6a6457858310c99e21', 'Commune', 620, 0),
(11, '72974a1cfcafcf280611b10a82e74b54', 'Village', 621, 0),
(12, '520a420b8e6be6b3c3ea66ab99e57435', 'Customer Group', 115, 1),
(13, '42304babf34e72c1a4dce50701e12839', 'Product', 110, 1),
(14, 'e3264cc59486b703afcdeaacb996710e', 'Customer', 116, 1),
(15, '8365d2a8d77a09b89fcee50655004a27', 'Physical Count', 148, 1),
(17, 'f9a88f87d0cc582efeda8c12938aa8c8', 'Product Group', 111, 1),
(18, '81459b6a6773e108948a55233bf12fac', 'Expense Type', 112, 1),
(19, '99fa60b66b1c567d8c6606b599015700', 'Chart of Account Type', 610, 0),
(20, '6f303522b089c355ce0b9ee775104390', 'Chart of Account Group', 611, 0),
(21, 'cf222ae6aca9f9cf8378eac7cc5ce5b4', 'Purchase Bill', 136, 1),
(22, '227048616f2e5982aec00e6a871344a6', 'Vendor', 119, 1),
(23, '9c8b198a113f3b1176de2d7a79759afc', 'Purchase Receive', 138, 0),
(24, 'f5b62306d60fe4acb3114aeef6c999b4', 'Tranfer Order', 150, 1),
(25, '1b8f13717b4828a4058ac6960232d285', 'Sales / Invoice', 142, 1),
(26, 'dfaf70bedd335c7b50d1fcea59d005aa', 'Exchange Rate', 131, 1),
(27, 'f0fbd96854444ded89d4b1f9015f59d8', 'Journal Entry', 501, 0),
(28, '3a7b8aef75c850fc0c88329a2fe85317', 'Report', 156, 1),
(29, '4ffce8932839606f89751f42736355d7', 'Discount', 121, 0),
(30, 'd74478cfb1c30db869fec8308b8c327f', 'Transfer Receive', 152, 0),
(31, '9f7972357b8c39468a2472dd18be5cac', 'Company', 122, 1),
(32, 'a39f08532ceb2c6fe690afa31cb26abd', 'Section', 113, 1),
(33, '8ec799ee20c7073153ec5f0ebbd51b4c', 'Service', 114, 1),
(34, '2bdecfa9a72ff6bd8b86a80bd4aeb388', 'Point Of Sales', 141, 1),
(35, 'ea6a41562c81e4d7ca33294cd2db2cd5', 'Budget Plan (P&L)', 504, 0),
(36, 'c1c7a91afc467fa6f0d084ab3bd014fd', 'Class', 616, 0),
(37, 'e03f01da6a6ead3cf97df9dd2dcf4eec', 'ICS', 127, 1),
(39, '15a3e6e85f6dd209b8aaa2fe32b7fee9', 'Payment Terms', 133, 1),
(40, 'cef51987a55a1bf11e7ec63d099d088d', 'Sales Return', 145, 1),
(41, '06bbc58a9063a5c81830e359fcfb2a35', 'Purchase Return', 140, 1),
(42, '0ae216357db5ddba0b7196bc7bb235aa', 'Receive Payments', 147, 1),
(43, '47231df39c02d54644d699f53c5eef79', 'Pay Bills', 139, 1),
(44, 'b619080f1b8c123b756ca58376abc381', 'Receive Payments (Journal)', 502, 0),
(45, 'b6dae7ffc7b5af48770ff1cd85382168', 'Pay Bills (Journal)', 503, 0),
(46, 'd1e26553adb9b5b5e9314d79f1471026', 'Other', 615, 0),
(47, '9b80424b4d8f8b9526a470b085350b8f', 'Employee', 613, 1),
(48, 'c1cd5c4e80d0536965184f4975e3cbda', 'Purchase Order', 135, 0),
(49, '062b294e776383e30b6e7e1c97759036', 'Employee Group', 614, 1),
(50, 'b829d445ec6ea2cb059a6cc6e69ccea1', 'Reconcile', 505, 0),
(51, 'a04bc6c93484ca3769e5a1e49fb7c5f2', 'Fixed Asset', 506, 0),
(52, 'bad2974741cd1503e9e3720affe0bd2d', 'Account Closing Date', 507, 0),
(53, '780c49d38114cc4eccd702117a61a40d', 'Delivery Note', 144, 1),
(54, '638476391742c884bcda68c7672a9e4b', 'Vendor Group', 118, 1),
(55, '65008ff5e04129f42c0a1f21a462b9ca', 'Price Type', 125, 1),
(57, '41815c1e6ed58e7029e22fcf9d37cdae', 'Shipment', 612, 0),
(58, 'b532b18fca7757cb81a3fd6b1cf30c64', 'Street', 612, 0),
(59, '5186469bac1df99f291464ec1ef28911', 'Reason', 126, 1),
(60, '6f2b23fc498b7a59fc9bda5415ed9000', 'Place', 612, 0),
(61, 'd0542ec2103ebd71144e0ad5fac5ba31', 'Position', 612, 1),
(68, 'c97cfb3f2136fa05c76c211af2b21465', 'Quotation', 301, 0),
(69, '1dc00756d9b6e14b20a330923097ce04', 'Sales Order', 301, 1),
(70, 'f7912832c3ece3a7d94fa0d409ca7f2a', 'Request Stock', 206, 0),
(71, 'b4c9be8b927f4867f8312419c762c8a1', 'VAT Setting', 132, 1),
(72, 'd9e6bef64986677b91b80697a6461819', 'Terms & Condition', 128, 1),
(73, 'cdc646029b25022aefc6af5e4192da67', 'Terms & Condition Type', 129, 1),
(74, '1a560bace2e34779226f3f133a93913f', 'Terms & Condition Apply', 130, 1),
(75, '60c21d7d0db2bf1e9bf98aa798be3cae', 'Customer Contact', 117, 0),
(77, 'fe4e44fbc99a2a459130ee5cac9f0500', 'Currency Setting', 607, 0),
(78, 'dfed48b75b07a5a574360c62d649dd4e', 'Branch Currency', 124, 1),
(79, 'f3be999828275d5bddb6b0c34e631107', 'Sales Target', 607, 0),
(80, '0a16b73b0787a25d44ecf542aa6cde30', 'Product Dashboard', 112, 1),
(81, '245ef33df29821ca493868b72b9ee7fb', 'Request Stock Dashboard', 201, 0),
(82, '4a625d12ddfda752f9af93334f8085b8', 'Inventory Adjustment Dashboard', 149, 0),
(83, '1e0052e7d54c07fe132070a33873a4f7', 'Transfer Order Dashboard', 151, 0),
(84, '70d600f052e49ef31f4def4456a72f6a', 'Quotation Dashboard', 201, 0),
(85, 'b38d1f567ca4800536cce4702cba1770', 'Sales Order Dashboard', 201, 0),
(86, '687eebf3ebcdfa0abe743a3138a3ba58', 'Sales/Invoice Dashboard', 143, 0),
(87, 'cbb80c1ce94eb214d7dbafd2be9826c5', 'Credit Memo Dashboard', 146, 0),
(88, '6806ca9baf293bdb131d25fe9210d900', 'Purchase Bill Dashboard', 137, 0),
(89, '0f8e73d6ea007c2b9be02e66b16cf053', 'Branch', 123, 1),
(90, '6933119a78401703c4a26feb8908905f', 'Branch Type', 311, 0),
(91, 'e99250ccbd8294ef282c39439320edd4', 'Customer Consignment', 311, 0),
(92, '4979a4c378c8b6d63ec482555e81fcc4', 'Customer Return Consignment', 311, 0),
(93, '7d4c6af7db0a1164ecc1ed24854587f1', 'Vendor Consignment', 311, 0),
(94, '1af5788af0df542c9f8d8bb694162f2c', 'Vendor Consignment Return', 311, 0),
(95, '5a16c2977a07ad58bc579d32629060a0', 'Landed Cost Type', 311, 0),
(96, '6400dbdfcf5db1370ec166fdaf9c18b2', 'Landed Cost', 311, 0),
(97, 'bd7f8bf60dfe317b26a0e031e3ee366f', 'Warehouse Type', 104, 1),
(98, 'e6b667483306bc674be8fbc8a43c213e', 'E-Commerce', 311, 0),
(99, '5eb91186938c6506e1112dea47a782c8', 'SYNC Monitoring', 160, 0),
(100, 'b7605c5315b1964c2190ba1092a2c0a0', 'Sales / Invoice Consignment', 304, 0),
(101, '690e2662204d5ddfc001ac85c4be1db0', 'Vendor Contact', 120, 0),
(102, '2c3809cf967c5d8c501aa69bb090ae2a', 'Warehouse Map', 105, 1),
(103, '3bd2edeb8ac5c64e5ae218055aaacfb0', 'General Setting', 134, 1),
(104, '8b7fc892b662442846726fd1161ea2a5', 'ShiftControl', 153, 1),
(105, 'e9dfe8c4339028e3f76f45585452f5dd', 'Collect Shift By User', 154, 1),
(106, '13a43869ee001ef288f44bcb92ef2cc7', 'Color', 311, 0),
(107, '67ad425d1b41893b35dc9b94da6d2db9', 'Cash Expense', 155, 1),
(108, 'c0d20505cb718b43bff662376748b55c', 'Dashboard', 157, 1),
(109, '3a22a4a7c64e025cde1fad06c4e2b00e', 'Inventory (Sales Mix)', 158, 1),
(110, '808a1d87e5b6ddb0c878a699151d3a70', 'Brand', 159, 1),
(111, 'a23612854862345fc7838cd39ac0a3e6', 'Patient', 700, 1),
(112, 'a23612854862345fc7838cd39ac0a3e6', 'Cashier', 701, 1),
(113, 'a23612854862345fc7838cd39ac0a3e6', 'Dashboard Table', 710, 1),
(114, 'a23612854862345fc7838cd39ac0a3e6', 'Doctor', 720, 1),
(115, 'a23612854862345fc7838cd39ac0a3e6', 'Labo', 730, 1),
(116, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Item', 740, 1),
(117, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Item Category', 750, 1),
(121, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Item Group', 760, 1),
(122, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Title Group', 770, 1),
(123, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Title Item', 780, 1),
(124, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Unit', 790, 1),
(125, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Age', 800, 1),
(126, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Medicine', 810, 1),
(127, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Site', 820, 1),
(128, 'a23612854862345fc7838cd39ac0a3e6', 'Labo Sub Title Group', 830, 1),
(129, 'a23612854862345fc7838cd39ac0a3e6', 'Patient History', 700, 1),
(130, 'a23612854862345fc7838cd39ac0a3e6', 'Appointment', 840, 1),
(131, 'a23612854862345fc7838cd39ac0a3e6', 'Company Insurance', 850, 1),
(132, 'a23612854862345fc7838cd39ac0a3e6', 'Insurance Group', 860, 1),
(133, 'a23612854862345fc7838cd39ac0a3e6', 'Insurance Service Price', 870, 1),
(134, 'a23612854862345fc7838cd39ac0a3e6', 'System Follow', 900, 1),
(135, 'a23612854862345fc7838cd39ac0a3e6', 'Nurse', 721, 1),
(136, 'a23612854862345fc7838cd39ac0a3e6', 'Echography Infomation', 725, 1),
(137, 'a23612854862345fc7838cd39ac0a3e6', 'Indication', 726, 1),
(138, 'a23612854862345fc7838cd39ac0a3e6', 'Echo Service', 727, 1),
(139, 'a23612854862345fc7838cd39ac0a3e6', 'Echo Service Cardia', 728, 1),
(140, 'a23612854862345fc7838cd39ac0a3e6', 'Echographie Patient Obstetnique', 729, 1),
(141, 'a23612854862345fc7838cd39ac0a3e6', 'X-Ray Service', 730, 1),
(142, 'a23612854862345fc7838cd39ac0a3e6', 'Mid Wife Service', 731, 1),
(143, 'a23612854862345fc7838cd39ac0a3e6', 'Doctor Consultation', 910, 1),
(144, 'a23612854862345fc7838cd39ac0a3e6', 'Chief Complain', 911, 1),
(145, 'a23612854862345fc7838cd39ac0a3e6', 'Diagnostic', 913, 1),
(146, 'a23612854862345fc7838cd39ac0a3e6', 'Examination', 912, 1),
(147, NULL, 'Cystoscopy Service', 731, 1),
(148, NULL, 'Uroflowmetry Service', 731, 1),
(149, 'a226128541492345fc7838cd39ac0a3e6', 'Patient IPD', 711, 1),
(150, 'a116128541492345fc7838cd39ac0a3e6', 'Patient Emergency', 710, 1),
(151, 'a226138541492345fc7838cd39ac0a3e6', 'Patient IPD Certificate', 711, 1),
(152, 'a226138541492345fc7838cd392c0a3e6', 'Room', 980, 1),
(153, NULL, 'Medical History', 914, 1),
(154, NULL, 'Doctor Comments', 915, 1),
(155, NULL, 'Frequency', 915, 1),
(156, NULL, 'Daily Clinical Report', 916, 1),
(157, NULL, 'Referrals', 917, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nationalities`
--

CREATE TABLE IF NOT EXISTS `nationalities` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=233 ;

--
-- Dumping data for table `nationalities`
--

INSERT INTO `nationalities` (`id`, `name`) VALUES
(1, 'Afghanistan'),
(2, 'Albania'),
(3, 'Algeria'),
(4, 'American Samoa'),
(5, 'Andorra'),
(6, 'Angola'),
(7, 'Anguilla'),
(8, 'Antarctica'),
(9, 'Antigua & barbuda'),
(10, 'Argentina'),
(11, 'Armenia'),
(12, 'Aruba'),
(13, 'Australia'),
(14, 'Austria'),
(15, 'Azerbaijan'),
(16, 'Bahamas'),
(17, 'Bahrain'),
(18, 'Bangladesh'),
(19, 'Barbados'),
(20, 'Belarus'),
(21, 'Belgium'),
(22, 'Belize'),
(23, 'Benin'),
(24, 'Bermuda'),
(25, 'Bhutan'),
(26, 'Bolivia'),
(27, 'Bosnia herzegovina'),
(28, 'Botswana'),
(29, 'Bouvet Island'),
(30, 'Brazil'),
(31, 'Brunei Darussalam'),
(32, 'Bulgaria'),
(33, 'Burkinafaso'),
(34, 'Burma'),
(35, 'Burundi'),
(36, 'Cambodia'),
(37, 'Cameroon'),
(38, 'Canada'),
(39, 'Cape Verde'),
(40, 'Cayman Islands'),
(41, 'Central african rep'),
(42, 'Chad'),
(43, 'Chile'),
(44, 'China'),
(45, 'Christmas Island'),
(46, 'Colombia'),
(47, 'Comoros'),
(48, 'Congo'),
(49, 'Cook Islands'),
(50, 'Costa Rica'),
(51, 'Cote D''Ivoire'),
(52, 'Croatia'),
(53, 'Cuba'),
(54, 'Cyprus'),
(55, 'Czech Republic'),
(56, 'Demrepcongo'),
(57, 'Denmark'),
(58, 'Djibouti'),
(59, 'Dominica'),
(60, 'East Timor'),
(61, 'Ecuador'),
(62, 'Egypt'),
(63, 'El Salvador'),
(64, 'Equatorial Guinea'),
(65, 'Eritrea'),
(66, 'Estonia'),
(67, 'Ethiopia'),
(68, 'Faroe Islands'),
(69, 'Fiji'),
(70, 'Finland'),
(71, 'France'),
(72, 'France, Metropolitan'),
(73, 'French Guiana'),
(74, 'French Polynesia'),
(75, 'Gabon'),
(76, 'Gambia'),
(77, 'Georgia'),
(78, 'Germany'),
(79, 'Ghana'),
(80, 'Gibraltar'),
(81, 'Greece'),
(82, 'Greenland'),
(83, 'Grenada'),
(84, 'Grenadines'),
(85, 'Guadeloupe'),
(86, 'Guam'),
(87, 'Guatemala'),
(88, 'Guinea'),
(89, 'Guinea-bissau'),
(90, 'Guyana'),
(91, 'Haiti'),
(92, 'Honduras'),
(93, 'Hong Kong'),
(94, 'Hungary'),
(95, 'Iceland'),
(96, 'India'),
(97, 'Indonesia'),
(98, 'Iran'),
(99, 'Iraq'),
(100, 'Ireland'),
(101, 'Israel'),
(102, 'Italy'),
(103, 'Ivory Coast'),
(104, 'Jamaica'),
(105, 'Japan'),
(106, 'Jordan'),
(107, 'Kazakhstan'),
(108, 'Kenya'),
(109, 'Kiribati'),
(110, 'Kuwait'),
(111, 'Kyrgyzstan'),
(112, 'Laos'),
(113, 'Latvia'),
(114, 'Lebanon'),
(115, 'Lesotho'),
(116, 'Liberia'),
(117, 'Libya'),
(118, 'Liechtenstein'),
(119, 'Lithuania'),
(120, 'Luxembourg'),
(121, 'Macadonia'),
(122, 'Macau'),
(123, 'Madagascar'),
(124, 'Malawi'),
(125, 'Malaysia'),
(126, 'Maldives'),
(127, 'Mali'),
(128, 'Malta'),
(129, 'Marshall Islands'),
(130, 'Martinique'),
(131, 'Mauritania'),
(132, 'Mauritius'),
(133, 'Mayotte'),
(134, 'Mexico'),
(135, 'Micronesia'),
(136, 'Moldova'),
(137, 'Monaco'),
(138, 'Mongolia'),
(139, 'Montserrat'),
(140, 'Morocco'),
(141, 'Mozambique'),
(142, 'Myanmar'),
(143, 'Namibia'),
(144, 'Nauru'),
(145, 'Nepal'),
(146, 'Neth Antilles'),
(147, 'Netherlands'),
(148, 'New Caledonia'),
(149, 'New Zealand'),
(150, 'Nicaragua'),
(151, 'Niger'),
(152, 'Nigeria'),
(153, 'Niue'),
(154, 'Norfolk Island'),
(155, 'North Korea'),
(156, 'Norway'),
(157, 'Oman'),
(158, 'Pakistan'),
(159, 'Palau'),
(160, 'Panama'),
(161, 'Papua Newguinea'),
(162, 'Paraguay'),
(163, 'Peru'),
(164, 'Philippines'),
(165, 'Pitcairn'),
(166, 'Poland'),
(167, 'Portugal'),
(168, 'Puerto Rico'),
(169, 'Qatar'),
(170, 'Rawanda'),
(171, 'République démocratique du Congo'),
(172, 'Reunion'),
(173, 'Romania'),
(174, 'Russian Federation'),
(175, 'Saint Kitts and Nevis'),
(176, 'Saint Lucia'),
(177, 'Samoa'),
(178, 'San Marino'),
(179, 'Sao Tome'),
(180, 'Saudi Arabia'),
(181, 'Senegal'),
(182, 'Serbia'),
(183, 'Seychelles'),
(184, 'Sierra Leone'),
(185, 'Singapore'),
(186, 'Slovakia'),
(187, 'Slovenia'),
(188, 'Solomon Islands'),
(189, 'Somalia'),
(190, 'South Africa'),
(191, 'South Korea'),
(192, 'Spain'),
(193, 'Sri Lanka'),
(194, 'St. Helena'),
(195, 'Stkitts Nevis'),
(196, 'Sudan'),
(197, 'Suriname'),
(198, 'Swaziland'),
(199, 'Sweden'),
(200, 'Switzerland'),
(201, 'Syria'),
(202, 'Taiwan'),
(203, 'Tajikistan'),
(204, 'Tanzania'),
(205, 'Thailand'),
(206, 'Togo'),
(207, 'Tokelau'),
(208, 'Tonga'),
(209, 'Trinidad & Tobago'),
(210, 'Tunisia'),
(211, 'Turkey'),
(212, 'Turkmenistan'),
(213, 'Tuvala'),
(214, 'Uganda'),
(215, 'Ukraine'),
(216, 'United Arab Emerates'),
(217, 'United Kingdom'),
(218, 'United States'),
(219, 'Uruguay'),
(220, 'Ussr'),
(221, 'Uzbekistan'),
(222, 'Vanuatu'),
(223, 'Venezuela'),
(224, 'Viet Nam'),
(225, 'Virgin Islands (British)'),
(226, 'Virgin Islands (U.S.)'),
(227, 'Western Sahara'),
(228, 'Yemen'),
(229, 'Yugoslavia'),
(230, 'Zaire'),
(231, 'Zambia'),
(232, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_contact_id` int(11) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `quotation_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `discount` decimal(15,3) DEFAULT NULL,
  `discount_percent` decimal(6,3) DEFAULT NULL,
  `total_vat` decimal(15,3) DEFAULT NULL,
  `vat_percent` decimal(5,3) DEFAULT NULL,
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` int(11) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `is_close` tinyint(4) DEFAULT '0',
  `is_approve` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `patient_id` int(11) DEFAULT NULL,
  `queue_id` int(11) DEFAULT NULL,
  `queue_doctor_id` int(11) DEFAULT NULL,
  `prescription_type` tinyint(4) DEFAULT '0' COMMENT '0 is Home medical; 1 is Hospital medical',
  PRIMARY KEY (`id`),
  KEY `key_search` (`quotation_number`,`order_code`,`order_date`,`is_close`,`status`),
  KEY `key_filter` (`company_id`,`customer_id`,`customer_contact_id`,`quotation_id`,`currency_center_id`,`vat_setting_id`,`price_type_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE IF NOT EXISTS `order_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_cost` decimal(15,3) DEFAULT '0.000',
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_days` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `morning` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `afternoon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `evening` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `night` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `morning_use_id` int(11) DEFAULT NULL,
  `afternoon_use_id` int(11) DEFAULT NULL,
  `evening_use_id` int(11) DEFAULT NULL,
  `night_use_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_miscs`
--

CREATE TABLE IF NOT EXISTS `order_miscs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_days` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `morning` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `afternoon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `evening` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `night` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `morning_use_id` int(11) DEFAULT NULL,
  `afternoon_use_id` int(11) DEFAULT NULL,
  `evening_use_id` int(11) DEFAULT NULL,
  `night_use_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_services`
--

CREATE TABLE IF NOT EXISTS `order_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_term_conditions`
--

CREATE TABLE IF NOT EXISTS `order_term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_search` (`order_id`,`term_condition_type_id`,`term_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `others`
--

CREATE TABLE IF NOT EXISTS `others` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `other_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `other_code` (`other_code`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `others`
--
DROP TRIGGER IF EXISTS `zOtherBfInsert`;
DELIMITER //
CREATE TRIGGER `zOtherBfInsert` BEFORE INSERT ON `others`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `other_companies`
--

CREATE TABLE IF NOT EXISTS `other_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `other_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `other_id` (`other_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `other_companies`
--
DROP TRIGGER IF EXISTS `zOtherCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zOtherCompanyBfInsert` BEFORE INSERT ON `other_companies`
 FOR EACH ROW BEGIN
	IF NEW.other_id = "" OR NEW.other_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `other_service_requests`
--

CREATE TABLE IF NOT EXISTS `other_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:is_active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `other_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `other_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:is_active 2:edit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `patients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_code` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `patient_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mother_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `father_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT '0',
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `relation_patient` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `patient_id_card` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `place_of_birth` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `religion` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `patient_bill_type_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `insurance_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `occupation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `father_occupation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mother_occupation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `patient_fax_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `case_emergency_tel` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `case_emergency_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allergic_medicine` tinyint(4) DEFAULT '0',
  `allergic_food` tinyint(4) DEFAULT '0',
  `allergic_medicine_note` text COLLATE utf8_unicode_ci,
  `allergic_food_note` text COLLATE utf8_unicode_ci,
  `unknown_allergic` tinyint(4) DEFAULT '0',
  `patient_type_id` int(11) DEFAULT '2',
  `patient_group_id` int(11) DEFAULT NULL,
  `patient_group` tinyint(4) DEFAULT '1' COMMENT '1: for adult, 2: for pediatric',
  `payment_term_id` int(11) DEFAULT '1',
  `payment_every` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referral_id` int(11) DEFAULT NULL,
  `register_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_code` (`patient_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_code`, `patient_name`, `mother_name`, `father_name`, `sex`, `telephone`, `address`, `location_id`, `province_id`, `district_id`, `commune_id`, `village_id`, `relation_patient`, `patient_id_card`, `email`, `dob`, `place_of_birth`, `nationality`, `religion`, `patient_bill_type_id`, `company_insurance_id`, `insurance_note`, `occupation`, `father_occupation`, `mother_occupation`, `patient_fax_number`, `case_emergency_tel`, `case_emergency_name`, `allergic_medicine`, `allergic_food`, `allergic_medicine_note`, `allergic_food_note`, `unknown_allergic`, `patient_type_id`, `patient_group_id`, `patient_group`, `payment_term_id`, `payment_every`, `photo`, `referral_id`, `register_date`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '23P0000001', 'Sen Meta', 'Chan Sreylin', 'Sen Dina', 'M', '012253621', '', NULL, 26, 9, 51, NULL, NULL, NULL, NULL, '2008-04-10', NULL, 36, '', NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 0, 0, '', '', 1, 2, 1, 1, 1, NULL, NULL, 4, '2023-06-07', '2023-06-07 12:19:18', 1, '2023-06-07 15:26:12', NULL, 1),
(2, '23P0000002', 'Roth Borin', 'Ny Linda', 'Try Roth', 'M', '015263985', '', NULL, 34, 81, 5, NULL, NULL, NULL, NULL, '2000-06-23', NULL, 36, '', NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 1, 0, 'Sea food, Beer', '', 0, 2, 1, 1, 1, NULL, NULL, 6, '2023-06-07', '2023-06-07 14:04:01', 1, '2023-06-07 15:25:33', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patient_bill_types`
--

CREATE TABLE IF NOT EXISTS `patient_bill_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `patient_bill_types`
--

INSERT INTO `patient_bill_types` (`id`, `name`, `description`, `created`, `modified`, `status`) VALUES
(1, 'Self', NULL, '2015-03-10 10:46:51', NULL, 1),
(2, 'Contract  Company', NULL, '2015-03-10 10:47:11', NULL, 2),
(3, 'Insurance Company', NULL, '2015-03-10 10:47:38', NULL, 1),
(4, 'Other', NULL, '2015-03-10 10:47:45', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patient_connection_details`
--

CREATE TABLE IF NOT EXISTS `patient_connection_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `patient_connection_with_hospital_id` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_connection_with_hospitals`
--

CREATE TABLE IF NOT EXISTS `patient_connection_with_hospitals` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `patient_connection_with_hospitals`
--

INSERT INTO `patient_connection_with_hospitals` (`id`, `name`, `description`, `created`, `modified`, `status`) VALUES
(1, 'TV', NULL, '2015-03-10 11:41:30', NULL, 1),
(2, 'Magazine', NULL, '2015-03-10 11:41:39', NULL, 1),
(3, 'Newspaper', NULL, '2015-03-10 11:41:30', NULL, 1),
(4, 'Radio', NULL, '2015-03-10 11:41:39', NULL, 1),
(5, 'Billboard', NULL, '2015-03-10 11:42:13', NULL, 1),
(6, 'Friend or Relative', NULL, '2015-03-10 11:42:24', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patient_consultations`
--

CREATE TABLE IF NOT EXISTS `patient_consultations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `consultation_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `doctor_consultation_ids` int(11) DEFAULT NULL,
  `date_first_complaint` date NOT NULL,
  `date_of_consult` date DEFAULT NULL,
  `physical_examination_id` int(11) DEFAULT NULL,
  `physical_examination` text COLLATE utf8_unicode_ci,
  `physical_examination_other_info` text COLLATE utf8_unicode_ci,
  `daignostic_id` int(11) DEFAULT NULL,
  `daignostic` text COLLATE utf8_unicode_ci,
  `daignostic_other_info` text COLLATE utf8_unicode_ci,
  `chief_complain_id` int(11) DEFAULT NULL,
  `chief_complain` text COLLATE utf8_unicode_ci,
  `medical_surgery` text COLLATE utf8_unicode_ci,
  `obstric_gynecologie` text COLLATE utf8_unicode_ci,
  `family_history` text COLLATE utf8_unicode_ci,
  `present_illness` text COLLATE utf8_unicode_ci,
  `medication` text COLLATE utf8_unicode_ci,
  `allergies` text COLLATE utf8_unicode_ci,
  `tob_alch_description` text COLLATE utf8_unicode_ci,
  `treatment` text COLLATE utf8_unicode_ci,
  `follow_up` text COLLATE utf8_unicode_ci,
  `remark` text COLLATE utf8_unicode_ci,
  `description_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treat` text COLLATE utf8_unicode_ci,
  `medical_history` text COLLATE utf8_unicode_ci,
  `past_medical_history` text COLLATE utf8_unicode_ci,
  `consult_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: OPD, 2: IPD',
  `room_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_followups`
--

CREATE TABLE IF NOT EXISTS `patient_followups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` bigint(20) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `patient_consultation_id` bigint(20) DEFAULT NULL,
  `followup` text COLLATE utf8_unicode_ci,
  `diagnosis` text COLLATE utf8_unicode_ci,
  `treatment` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_groups`
--

CREATE TABLE IF NOT EXISTS `patient_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `patient_groups`
--

INSERT INTO `patient_groups` (`id`, `name`, `description`, `created`, `modified`, `status`) VALUES
(1, 'Cambodian', 'patient are in our local', '2015-03-19 13:26:39', NULL, 1),
(2, 'Foreigner', 'patient are foreigner', '2015-03-19 13:28:28', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patient_ipds`
--

CREATE TABLE IF NOT EXISTS `patient_ipds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL COMMENT 'department id',
  `allergies` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_ipd` datetime DEFAULT NULL,
  `ipd_code` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `witness_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorized_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorized_telephone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorized_address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorized_id_card` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorized_issue_date` date DEFAULT NULL,
  `authorized_expiration_date` date DEFAULT NULL,
  `authorized_issue_place` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `doctor_explain_to_patient` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `patient_following_surgical` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `according_to_patient` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `according_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipd_type` tinyint(4) DEFAULT '1' COMMENT '1 is admission consent form , 2 is Medical Surgery Consent form',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '0 deleted, 1 stayed IPD, 2 leaved from Hospital',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_ipd_certificates`
--

CREATE TABLE IF NOT EXISTS `patient_ipd_certificates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_ipd_id` int(11) DEFAULT NULL,
  `date_certificate_from` date DEFAULT NULL,
  `date_certificate_to` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_ipd_service_details`
--

CREATE TABLE IF NOT EXISTS `patient_ipd_service_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_ipd_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '1 is service, 2 is labo, 3 is pharmacy',
  `labo_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `discount` double DEFAULT '0',
  `date_created` date DEFAULT NULL,
  `unit_price` double DEFAULT '0',
  `total_price` double DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '0 deleted, 1 service still use in IPD, 2 paid some service',
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`patient_ipd_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_ipd_vital_signs`
--

CREATE TABLE IF NOT EXISTS `patient_ipd_vital_signs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_ipd_id` int(11) DEFAULT NULL,
  `bp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sop2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gas_fecal` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `drainage` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature` double DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_leaves`
--

CREATE TABLE IF NOT EXISTS `patient_leaves` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_nme` text COLLATE utf8_unicode_ci,
  `diagnotist_after` text COLLATE utf8_unicode_ci,
  `type_leave` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `end_date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `patient_ipd_id` int(11) DEFAULT NULL,
  `num_date` int(11) DEFAULT NULL,
  `transfer_to` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `section_patient` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treatment` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `time_create` time DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `patient_ipd_id` (`patient_ipd_id`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_locations`
--

CREATE TABLE IF NOT EXISTS `patient_locations` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sts` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Dumping data for table `patient_locations`
--

INSERT INTO `patient_locations` (`id`, `name`, `sts`) VALUES
(1, 'Phnom Penh', '1'),
(2, 'Banteay Meanchey', '1'),
(3, 'Battambong', '1'),
(4, 'Kampong Cham', '1'),
(5, 'Kampong Chhnang', '1'),
(6, 'Kampong Som', '1'),
(7, 'Kampong Speu', '1'),
(8, 'Kampong Thom', '1'),
(9, 'Kampot', '1'),
(10, 'Kandal', '1'),
(11, 'Koh Kong', '1'),
(12, 'Kratie', '1'),
(13, 'Mondulkiri', '1'),
(14, 'Pailin', '1'),
(15, 'Posat', '1'),
(16, 'Preah Vihear', '1'),
(17, 'Prey Veng', '1'),
(18, 'Ratanakiri', '1'),
(19, 'Sieam Reap', '1'),
(20, 'Stung Treng', '1'),
(21, 'Svay Rieang', '1'),
(22, 'Takeo', '1'),
(23, 'Udor Meanchey', '1');

-- --------------------------------------------------------

--
-- Table structure for table `patient_stay_in_rooms`
--

CREATE TABLE IF NOT EXISTS `patient_stay_in_rooms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_ipd_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0 is delete, 1 is on staying',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_types`
--

CREATE TABLE IF NOT EXISTS `patient_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `patient_types`
--

INSERT INTO `patient_types` (`id`, `name`, `description`, `created`, `modified`, `status`) VALUES
(1, 'IPD', NULL, '2015-03-10 10:49:06', NULL, 1),
(2, 'OPD', NULL, '2015-03-10 10:49:16', NULL, 1),
(3, 'Emergency', NULL, '2015-03-10 10:49:55', NULL, 1),
(4, 'Other', NULL, '2015-03-10 10:50:06', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patient_vital_signs`
--

CREATE TABLE IF NOT EXISTS `patient_vital_signs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `height` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `BMI` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pulse` double DEFAULT NULL,
  `respiratory` double DEFAULT NULL,
  `sop2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature` double DEFAULT NULL,
  `other_info` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_vital_sign_blood_pressures`
--

CREATE TABLE IF NOT EXISTS `patient_vital_sign_blood_pressures` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_vital_sign_id` int(11) DEFAULT NULL,
  `result_systolic_1` double DEFAULT NULL,
  `result_systolic_2` double DEFAULT NULL,
  `result_systolic_3` double DEFAULT NULL,
  `result_diastolic_1` double DEFAULT NULL,
  `result_diastolic_2` double DEFAULT NULL,
  `result_diastolic_3` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_terms`
--

CREATE TABLE IF NOT EXISTS `payment_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `net_days` int(11) DEFAULT NULL,
  `discount_percent` decimal(5,3) DEFAULT '0.000',
  `discount_days` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `payment_terms`
--

INSERT INTO `payment_terms` (`id`, `sys_code`, `name`, `net_days`, `discount_percent`, `discount_days`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '9372fb3f5384ecc646eb271109184f32', 'COD', 0, '0.000', NULL, '2016-08-11 15:28:18', 1, '2016-08-11 15:28:22', NULL, 1),
(2, '74c1fce1d16b614abce224d10eff5d91', 'Relay', 7, '0.000', NULL, '2019-05-03 16:17:25', 1, '2019-05-03 16:17:25', NULL, 1);

--
-- Triggers `payment_terms`
--
DROP TRIGGER IF EXISTS `zPaymentTermBfInsert`;
DELIMITER //
CREATE TRIGGER `zPaymentTermBfInsert` BEFORE INSERT ON `payment_terms`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.net_days = "" OR NEW.net_days = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zTermBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zTermBeforeDelete` BEFORE DELETE ON `payment_terms`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete default term';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zTermBeforeUpdate`;
DELIMITER //
CREATE TRIGGER `zTermBeforeUpdate` BEFORE UPDATE ON `payment_terms`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot update default term';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pay_bills`
--

CREATE TABLE IF NOT EXISTS `pay_bills` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`location_id`,`vendor_id`),
  KEY `searchs` (`date`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pay_bill_details`
--

CREATE TABLE IF NOT EXISTS `pay_bill_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pay_bill_id` bigint(20) DEFAULT NULL,
  `purchase_order_id` bigint(20) DEFAULT NULL,
  `amount_due` decimal(15,3) DEFAULT '0.000',
  `paid` decimal(15,3) DEFAULT '0.000',
  `balance` decimal(15,3) DEFAULT '0.000',
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filters` (`pay_bill_id`,`purchase_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id_module_id` (`group_id`,`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43151 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `group_id`, `module_id`) VALUES
(42704, 1, 1),
(42705, 1, 2),
(42708, 1, 6),
(42712, 1, 10),
(42713, 1, 11),
(42714, 1, 12),
(42715, 1, 13),
(42719, 1, 14),
(42720, 1, 15),
(42721, 1, 16),
(42722, 1, 17),
(42725, 1, 18),
(42726, 1, 19),
(42727, 1, 20),
(42728, 1, 21),
(42730, 1, 22),
(42731, 1, 23),
(42732, 1, 24),
(42733, 1, 25),
(42735, 1, 46),
(42736, 1, 47),
(42737, 1, 48),
(42738, 1, 49),
(42746, 1, 55),
(42751, 1, 60),
(42752, 1, 61),
(42753, 1, 62),
(42754, 1, 63),
(42772, 1, 72),
(42773, 1, 73),
(42774, 1, 74),
(42775, 1, 75),
(42756, 1, 76),
(42757, 1, 77),
(42758, 1, 78),
(42759, 1, 79),
(42747, 1, 88),
(42761, 1, 91),
(42762, 1, 92),
(42763, 1, 94),
(42777, 1, 101),
(42778, 1, 102),
(42779, 1, 103),
(42780, 1, 104),
(42782, 1, 105),
(42783, 1, 106),
(42784, 1, 107),
(42785, 1, 108),
(42792, 1, 109),
(42793, 1, 110),
(42794, 1, 117),
(42795, 1, 119),
(42836, 1, 128),
(42837, 1, 130),
(42796, 1, 134),
(42739, 1, 136),
(42838, 1, 137),
(42839, 1, 138),
(42840, 1, 139),
(42841, 1, 140),
(42843, 1, 141),
(42844, 1, 142),
(42845, 1, 143),
(42846, 1, 144),
(42764, 1, 145),
(42786, 1, 147),
(42787, 1, 148),
(42848, 1, 149),
(42849, 1, 151),
(42850, 1, 152),
(42797, 1, 153),
(42798, 1, 154),
(42788, 1, 164),
(42799, 1, 166),
(42800, 1, 167),
(42801, 1, 169),
(42802, 1, 170),
(42803, 1, 171),
(42804, 1, 173),
(42858, 1, 176),
(42789, 1, 181),
(42851, 1, 182),
(42805, 1, 192),
(42860, 1, 193),
(42861, 1, 194),
(42862, 1, 195),
(42863, 1, 196),
(42806, 1, 197),
(42807, 1, 198),
(42760, 1, 199),
(42765, 1, 201),
(42865, 1, 202),
(42866, 1, 203),
(42867, 1, 204),
(42875, 1, 205),
(42876, 1, 206),
(42877, 1, 207),
(42859, 1, 208),
(42884, 1, 209),
(42885, 1, 210),
(42790, 1, 211),
(42808, 1, 212),
(42809, 1, 213),
(42810, 1, 214),
(42811, 1, 215),
(42868, 1, 216),
(42869, 1, 217),
(42878, 1, 218),
(42879, 1, 219),
(42870, 1, 220),
(42871, 1, 221),
(42880, 1, 222),
(42881, 1, 223),
(42852, 1, 226),
(42812, 1, 230),
(42813, 1, 232),
(42814, 1, 238),
(42815, 1, 239),
(42816, 1, 240),
(42817, 1, 242),
(42818, 1, 243),
(42748, 1, 246),
(42886, 1, 247),
(42887, 1, 248),
(42888, 1, 249),
(42889, 1, 250),
(42819, 1, 251),
(42820, 1, 257),
(42821, 1, 258),
(42872, 1, 259),
(42822, 1, 264),
(42823, 1, 265),
(42824, 1, 268),
(42892, 1, 271),
(42893, 1, 272),
(42894, 1, 273),
(42895, 1, 274),
(42853, 1, 280),
(42897, 1, 282),
(42898, 1, 284),
(42900, 1, 286),
(42901, 1, 287),
(42902, 1, 288),
(42903, 1, 289),
(42873, 1, 291),
(42905, 1, 292),
(42906, 1, 293),
(42907, 1, 294),
(42908, 1, 295),
(42740, 1, 296),
(42729, 1, 297),
(42734, 1, 298),
(42716, 1, 299),
(42723, 1, 300),
(42890, 1, 301),
(42896, 1, 302),
(42864, 1, 303),
(42904, 1, 308),
(42776, 1, 309),
(42842, 1, 310),
(42847, 1, 311),
(42755, 1, 312),
(42741, 1, 313),
(42791, 1, 316),
(42766, 1, 317),
(42882, 1, 318),
(42749, 1, 322),
(42750, 1, 323),
(42854, 1, 328),
(42909, 1, 342),
(42910, 1, 343),
(42911, 1, 344),
(42912, 1, 345),
(42913, 1, 350),
(42914, 1, 351),
(42915, 1, 352),
(42916, 1, 353),
(42917, 1, 381),
(42918, 1, 382),
(42919, 1, 383),
(42920, 1, 384),
(42921, 1, 386),
(42922, 1, 387),
(42899, 1, 395),
(42874, 1, 396),
(42767, 1, 398),
(42883, 1, 399),
(42891, 1, 416),
(42923, 1, 419),
(42924, 1, 420),
(42925, 1, 422),
(42926, 1, 423),
(42742, 1, 425),
(42930, 1, 427),
(42931, 1, 428),
(42709, 1, 429),
(42710, 1, 430),
(42711, 1, 431),
(42706, 1, 433),
(42934, 1, 435),
(42935, 1, 436),
(42936, 1, 437),
(42937, 1, 438),
(42938, 1, 439),
(42939, 1, 440),
(42940, 1, 441),
(42941, 1, 442),
(42942, 1, 443),
(42943, 1, 444),
(42944, 1, 445),
(42945, 1, 446),
(42927, 1, 458),
(42928, 1, 459),
(42932, 1, 463),
(42933, 1, 464),
(42768, 1, 465),
(42769, 1, 466),
(42770, 1, 468),
(42946, 1, 473),
(42947, 1, 474),
(42948, 1, 475),
(42949, 1, 476),
(42950, 1, 477),
(42929, 1, 483),
(42707, 1, 484),
(42951, 1, 485),
(42952, 1, 486),
(42825, 1, 489),
(42743, 1, 490),
(42826, 1, 491),
(42827, 1, 494),
(42828, 1, 496),
(42829, 1, 497),
(42972, 1, 499),
(42953, 1, 509),
(42954, 1, 510),
(42955, 1, 511),
(42956, 1, 512),
(42957, 1, 513),
(42958, 1, 514),
(42959, 1, 559),
(42960, 1, 560),
(42961, 1, 561),
(42962, 1, 562),
(42963, 1, 563),
(42717, 1, 564),
(42744, 1, 567),
(42745, 1, 568),
(42718, 1, 591),
(42724, 1, 592),
(42781, 1, 593),
(42964, 1, 594),
(42855, 1, 595),
(42856, 1, 596),
(42965, 1, 597),
(42966, 1, 598),
(42967, 1, 599),
(42973, 1, 602),
(42968, 1, 607),
(42969, 1, 608),
(42970, 1, 609),
(42971, 1, 610),
(42974, 1, 611),
(42975, 1, 612),
(42976, 1, 613),
(42977, 1, 614),
(42978, 1, 615),
(42979, 1, 616),
(42980, 1, 617),
(42981, 1, 618),
(42982, 1, 619),
(42983, 1, 620),
(42857, 1, 621),
(42984, 1, 622),
(42985, 1, 623),
(42986, 1, 624),
(42987, 1, 625),
(42988, 1, 626),
(42989, 1, 627),
(42990, 1, 628),
(42991, 1, 629),
(42992, 1, 630),
(42993, 1, 631),
(42994, 1, 632),
(42995, 1, 633),
(42996, 1, 634),
(43000, 1, 635),
(43001, 1, 636),
(43002, 1, 637),
(43005, 1, 638),
(43006, 1, 639),
(43007, 1, 640),
(43008, 1, 641),
(43009, 1, 642),
(43010, 1, 643),
(43011, 1, 644),
(43012, 1, 645),
(43013, 1, 663),
(43014, 1, 664),
(43015, 1, 665),
(43016, 1, 666),
(43017, 1, 667),
(43018, 1, 668),
(43019, 1, 669),
(43020, 1, 670),
(43021, 1, 671),
(43022, 1, 672),
(43023, 1, 673),
(43024, 1, 674),
(43025, 1, 675),
(43026, 1, 676),
(43027, 1, 677),
(43028, 1, 678),
(43029, 1, 679),
(43030, 1, 680),
(43031, 1, 681),
(43032, 1, 682),
(43033, 1, 683),
(43034, 1, 684),
(43035, 1, 685),
(43036, 1, 686),
(43037, 1, 687),
(43038, 1, 688),
(43039, 1, 689),
(43040, 1, 690),
(43041, 1, 691),
(43042, 1, 692),
(43043, 1, 693),
(43044, 1, 694),
(43045, 1, 695),
(43046, 1, 696),
(43047, 1, 697),
(43048, 1, 698),
(43049, 1, 699),
(42997, 1, 700),
(42998, 1, 701),
(42999, 1, 702),
(43050, 1, 703),
(43051, 1, 704),
(43052, 1, 705),
(43053, 1, 706),
(43054, 1, 707),
(43056, 1, 708),
(43057, 1, 709),
(43058, 1, 710),
(43059, 1, 711),
(43060, 1, 712),
(43061, 1, 713),
(43062, 1, 714),
(43063, 1, 715),
(43064, 1, 716),
(43065, 1, 717),
(43066, 1, 718),
(43067, 1, 719),
(43068, 1, 720),
(43069, 1, 721),
(43070, 1, 722),
(42830, 1, 723),
(42831, 1, 724),
(42832, 1, 725),
(42833, 1, 726),
(43071, 1, 727),
(43072, 1, 728),
(43073, 1, 729),
(43074, 1, 730),
(43075, 1, 731),
(43076, 1, 732),
(43077, 1, 733),
(43078, 1, 734),
(43079, 1, 735),
(43080, 1, 736),
(43081, 1, 737),
(43082, 1, 738),
(43083, 1, 739),
(43084, 1, 740),
(43085, 1, 741),
(43086, 1, 742),
(43087, 1, 743),
(43088, 1, 744),
(43089, 1, 745),
(43090, 1, 746),
(43091, 1, 747),
(43092, 1, 748),
(43093, 1, 749),
(43094, 1, 750),
(43095, 1, 751),
(43096, 1, 752),
(43097, 1, 753),
(43098, 1, 754),
(43099, 1, 784),
(43100, 1, 785),
(43101, 1, 786),
(43102, 1, 787),
(43055, 1, 788),
(43103, 1, 789),
(43104, 1, 790),
(43105, 1, 791),
(43106, 1, 792),
(43107, 1, 793),
(43108, 1, 794),
(43109, 1, 795),
(43110, 1, 796),
(43111, 1, 797),
(43112, 1, 798),
(43113, 1, 799),
(43114, 1, 800),
(43115, 1, 811),
(43116, 1, 812),
(43117, 1, 813),
(43118, 1, 814),
(43119, 1, 815),
(43120, 1, 816),
(43121, 1, 817),
(43122, 1, 818),
(43123, 1, 819),
(43124, 1, 820),
(43125, 1, 830),
(43126, 1, 831),
(43127, 1, 832),
(43128, 1, 833),
(43003, 1, 834),
(43004, 1, 835),
(43129, 1, 836),
(43130, 1, 837),
(43131, 1, 838),
(43132, 1, 839),
(43133, 1, 840),
(43134, 1, 841),
(43135, 1, 842),
(43136, 1, 843),
(43137, 1, 844),
(43138, 1, 845),
(43139, 1, 846),
(43140, 1, 847),
(43141, 1, 848),
(43142, 1, 849),
(43143, 1, 850),
(43144, 1, 851),
(42834, 1, 852),
(43145, 1, 854),
(43146, 1, 855),
(43147, 1, 856),
(43148, 1, 857),
(43149, 1, 858),
(43150, 1, 859),
(42835, 1, 861),
(42771, 1, 862),
(34969, 2, 1),
(34970, 2, 381),
(34971, 2, 382),
(34972, 2, 383),
(34973, 2, 384),
(34974, 2, 386),
(34975, 2, 387),
(34976, 2, 419),
(34977, 2, 420),
(34978, 2, 422),
(34979, 2, 423),
(34980, 2, 458),
(34981, 2, 459),
(34982, 2, 483),
(34983, 2, 635),
(34984, 2, 636),
(34985, 2, 703),
(34986, 2, 704),
(34987, 2, 705),
(34988, 2, 706),
(34989, 2, 707),
(34990, 2, 730),
(34991, 2, 731),
(34992, 2, 732),
(34993, 2, 789),
(34994, 2, 790),
(34995, 2, 791),
(34996, 2, 792),
(34997, 2, 793),
(34998, 2, 794),
(34999, 2, 795),
(35000, 2, 796),
(35001, 2, 797),
(35002, 2, 798),
(35003, 2, 799),
(35004, 2, 800),
(35005, 2, 811),
(35006, 2, 812),
(35007, 2, 813),
(35008, 2, 815),
(35009, 2, 816),
(35010, 2, 817),
(35011, 2, 819),
(35012, 2, 820),
(35013, 2, 826),
(35014, 2, 827),
(35015, 2, 828),
(33919, 3, 1),
(33920, 3, 10),
(33921, 3, 11),
(33922, 3, 12),
(33923, 3, 13),
(33927, 3, 14),
(33928, 3, 15),
(33929, 3, 16),
(33930, 3, 17),
(33933, 3, 18),
(33934, 3, 19),
(33935, 3, 20),
(33936, 3, 21),
(33938, 3, 22),
(33939, 3, 23),
(33940, 3, 24),
(33941, 3, 25),
(33943, 3, 46),
(33944, 3, 47),
(33945, 3, 48),
(33946, 3, 49),
(33954, 3, 55),
(33960, 3, 60),
(33961, 3, 61),
(33962, 3, 62),
(33963, 3, 63),
(33980, 3, 72),
(33981, 3, 73),
(33982, 3, 74),
(33983, 3, 75),
(33965, 3, 76),
(33966, 3, 77),
(33967, 3, 78),
(33968, 3, 79),
(33955, 3, 88),
(33970, 3, 91),
(33971, 3, 92),
(33972, 3, 94),
(33985, 3, 105),
(33986, 3, 106),
(33987, 3, 107),
(33988, 3, 108),
(33998, 3, 109),
(33999, 3, 110),
(34000, 3, 117),
(34001, 3, 119),
(34002, 3, 134),
(33947, 3, 136),
(34042, 3, 137),
(34043, 3, 138),
(34044, 3, 139),
(34045, 3, 140),
(34047, 3, 141),
(34048, 3, 142),
(34049, 3, 143),
(34050, 3, 144),
(33973, 3, 145),
(33989, 3, 147),
(33990, 3, 148),
(34052, 3, 149),
(34053, 3, 151),
(34054, 3, 152),
(34003, 3, 153),
(34004, 3, 154),
(33991, 3, 164),
(34005, 3, 166),
(34006, 3, 167),
(34007, 3, 169),
(34008, 3, 170),
(34009, 3, 171),
(34010, 3, 173),
(34062, 3, 176),
(33992, 3, 181),
(34055, 3, 182),
(34011, 3, 192),
(34064, 3, 193),
(34065, 3, 194),
(34066, 3, 195),
(34067, 3, 196),
(34012, 3, 197),
(34013, 3, 198),
(33969, 3, 199),
(33974, 3, 201),
(34069, 3, 202),
(34070, 3, 203),
(34071, 3, 204),
(34080, 3, 205),
(34081, 3, 206),
(34082, 3, 207),
(34063, 3, 208),
(34089, 3, 209),
(34090, 3, 210),
(33993, 3, 211),
(34014, 3, 212),
(34015, 3, 213),
(34016, 3, 214),
(34017, 3, 215),
(34072, 3, 216),
(34073, 3, 217),
(34083, 3, 218),
(34084, 3, 219),
(34074, 3, 220),
(34075, 3, 221),
(34085, 3, 222),
(34086, 3, 223),
(34056, 3, 226),
(34018, 3, 230),
(34019, 3, 232),
(34020, 3, 238),
(34021, 3, 239),
(34022, 3, 240),
(34023, 3, 242),
(34024, 3, 243),
(33956, 3, 246),
(34091, 3, 247),
(34092, 3, 248),
(34093, 3, 249),
(34094, 3, 250),
(34025, 3, 251),
(34026, 3, 257),
(34027, 3, 258),
(34076, 3, 259),
(34028, 3, 264),
(34029, 3, 265),
(34030, 3, 268),
(34097, 3, 271),
(34098, 3, 272),
(34099, 3, 273),
(34100, 3, 274),
(34057, 3, 280),
(34102, 3, 282),
(34103, 3, 284),
(34106, 3, 286),
(34107, 3, 287),
(34108, 3, 288),
(34109, 3, 289),
(33994, 3, 290),
(34077, 3, 291),
(34111, 3, 292),
(34112, 3, 293),
(34113, 3, 294),
(34114, 3, 295),
(33948, 3, 296),
(33937, 3, 297),
(33942, 3, 298),
(33924, 3, 299),
(33931, 3, 300),
(34095, 3, 301),
(34101, 3, 302),
(34068, 3, 303),
(34110, 3, 308),
(33984, 3, 309),
(34046, 3, 310),
(34051, 3, 311),
(33964, 3, 312),
(33949, 3, 313),
(34078, 3, 315),
(33975, 3, 317),
(34087, 3, 318),
(34104, 3, 319),
(33957, 3, 321),
(33958, 3, 322),
(33959, 3, 323),
(34058, 3, 328),
(34115, 3, 342),
(34116, 3, 343),
(34117, 3, 344),
(34118, 3, 345),
(34119, 3, 350),
(34120, 3, 351),
(34121, 3, 352),
(34122, 3, 353),
(34123, 3, 381),
(34124, 3, 382),
(34125, 3, 383),
(34126, 3, 384),
(34127, 3, 386),
(34128, 3, 387),
(33995, 3, 394),
(34105, 3, 395),
(34079, 3, 396),
(33976, 3, 398),
(34088, 3, 399),
(34096, 3, 416),
(34129, 3, 419),
(34130, 3, 420),
(34131, 3, 422),
(34132, 3, 423),
(33950, 3, 425),
(34136, 3, 427),
(34137, 3, 428),
(34133, 3, 458),
(34134, 3, 459),
(33996, 3, 461),
(33997, 3, 462),
(34138, 3, 463),
(34139, 3, 464),
(33977, 3, 465),
(33978, 3, 466),
(33979, 3, 468),
(34135, 3, 483),
(34140, 3, 485),
(34141, 3, 486),
(34031, 3, 489),
(33951, 3, 490),
(34032, 3, 491),
(34033, 3, 494),
(34034, 3, 496),
(34035, 3, 497),
(34142, 3, 509),
(34143, 3, 510),
(34144, 3, 559),
(34145, 3, 560),
(34146, 3, 561),
(34147, 3, 562),
(34148, 3, 563),
(33925, 3, 564),
(33952, 3, 567),
(33953, 3, 568),
(33926, 3, 591),
(33932, 3, 592),
(34149, 3, 594),
(34059, 3, 595),
(34060, 3, 596),
(34150, 3, 598),
(34151, 3, 599),
(34036, 3, 600),
(34037, 3, 601),
(34152, 3, 607),
(34153, 3, 608),
(34154, 3, 609),
(34155, 3, 610),
(34061, 3, 621),
(34156, 3, 622),
(34157, 3, 623),
(34158, 3, 624),
(34159, 3, 625),
(34160, 3, 626),
(34161, 3, 627),
(34162, 3, 628),
(34163, 3, 629),
(34164, 3, 630),
(34165, 3, 631),
(34166, 3, 632),
(34167, 3, 633),
(34168, 3, 634),
(34169, 3, 701),
(34170, 3, 702),
(34171, 3, 703),
(34172, 3, 704),
(34173, 3, 705),
(34174, 3, 706),
(34175, 3, 707),
(34038, 3, 723),
(34039, 3, 724),
(34040, 3, 725),
(34041, 3, 726),
(33894, 4, 1),
(33895, 4, 626),
(33896, 4, 627),
(33897, 4, 628),
(33898, 4, 629),
(33899, 4, 630),
(33900, 4, 635),
(33901, 4, 703),
(33902, 4, 704),
(33903, 4, 705),
(33904, 4, 706),
(33905, 4, 707),
(33906, 4, 730),
(33907, 4, 731),
(33908, 4, 732),
(33909, 4, 811),
(33910, 4, 812),
(33911, 4, 813),
(33912, 4, 815),
(33913, 4, 816),
(33914, 4, 817),
(33915, 4, 819),
(33916, 4, 820),
(33917, 4, 826),
(33918, 4, 827),
(26458, 5, 1),
(26459, 5, 626),
(26460, 5, 627),
(26461, 5, 628),
(26462, 5, 629),
(26463, 5, 630),
(30301, 6, 1),
(30302, 6, 46),
(30303, 6, 47),
(30304, 6, 48),
(30305, 6, 136),
(30309, 6, 197),
(30310, 6, 240),
(30311, 6, 265),
(30306, 6, 296),
(30307, 6, 313),
(30314, 6, 381),
(30315, 6, 382),
(30316, 6, 383),
(30317, 6, 384),
(30318, 6, 386),
(30319, 6, 387),
(30320, 6, 419),
(30321, 6, 420),
(30322, 6, 422),
(30323, 6, 423),
(30308, 6, 425),
(30327, 6, 427),
(30324, 6, 458),
(30325, 6, 459),
(30326, 6, 483),
(30312, 6, 494),
(30313, 6, 496),
(30328, 6, 626),
(30329, 6, 627),
(30330, 6, 628),
(30331, 6, 629),
(30332, 6, 630),
(30333, 6, 634),
(30334, 6, 703),
(30335, 6, 704),
(30336, 6, 705),
(30337, 6, 706),
(30338, 6, 707),
(30339, 6, 788),
(34176, 7, 1),
(34179, 7, 637),
(34182, 7, 638),
(34183, 7, 639),
(34184, 7, 640),
(34185, 7, 641),
(34186, 7, 642),
(34187, 7, 643),
(34188, 7, 644),
(34189, 7, 645),
(34190, 7, 663),
(34191, 7, 664),
(34192, 7, 665),
(34193, 7, 666),
(34194, 7, 668),
(34195, 7, 669),
(34196, 7, 670),
(34197, 7, 671),
(34198, 7, 672),
(34199, 7, 673),
(34200, 7, 674),
(34201, 7, 675),
(34202, 7, 676),
(34203, 7, 677),
(34204, 7, 678),
(34205, 7, 679),
(34206, 7, 680),
(34207, 7, 681),
(34208, 7, 682),
(34209, 7, 683),
(34210, 7, 684),
(34211, 7, 685),
(34212, 7, 686),
(34213, 7, 687),
(34214, 7, 688),
(34215, 7, 689),
(34216, 7, 690),
(34217, 7, 691),
(34218, 7, 692),
(34219, 7, 693),
(34220, 7, 694),
(34221, 7, 695),
(34222, 7, 696),
(34223, 7, 697),
(34224, 7, 698),
(34225, 7, 699),
(34178, 7, 700),
(34177, 7, 724),
(34226, 7, 729),
(34180, 7, 834),
(34181, 7, 835),
(34227, 8, 1),
(34229, 8, 637),
(34230, 8, 638),
(34231, 8, 642),
(34232, 8, 663),
(34233, 8, 671),
(34234, 8, 675),
(34235, 8, 695),
(34228, 8, 700);

-- --------------------------------------------------------

--
-- Table structure for table `pgroups`
--

CREATE TABLE IF NOT EXISTS `pgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(10) DEFAULT NULL,
  `user_apply` tinyint(4) DEFAULT '0',
  `ics_apply_sub` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `parent_id` (`parent_id`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pgroups`
--

INSERT INTO `pgroups` (`id`, `sys_code`, `parent_id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `user_apply`, `ics_apply_sub`, `is_active`) VALUES
(1, '557993ef673763603b56cdc5ca266194', NULL, 'Medicine', '2023-02-09 14:44:21', 1, NULL, NULL, 0, 0, 1),
(2, '7b1517f8b1942cfedbd9c596a2ec8066', NULL, 'Vaccine', '2023-02-24 10:04:42', 1, '2023-02-24 10:04:42', NULL, 0, 0, 1);

--
-- Triggers `pgroups`
--
DROP TRIGGER IF EXISTS `zPgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zPgroupBfInsert` BEFORE INSERT ON `pgroups`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = "" THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pgroup_accounts`
--

CREATE TABLE IF NOT EXISTS `pgroup_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pgroup_id` int(11) NOT NULL,
  `account_type_id` int(11) NOT NULL,
  `chart_account_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `pgroup_accounts`
--
DROP TRIGGER IF EXISTS `zPgroupAccountBfInsert`;
DELIMITER //
CREATE TRIGGER `zPgroupAccountBfInsert` BEFORE INSERT ON `pgroup_accounts`
 FOR EACH ROW BEGIN
	IF NEW.pgroup_id = "" OR NEW.pgroup_id = NULL OR NEW.account_type_id = "" OR NEW.account_type_id = NULL OR NEW.chart_account_id = "" OR NEW.chart_account_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pgroup_companies`
--

CREATE TABLE IF NOT EXISTS `pgroup_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pgroup_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pgroup_id` (`pgroup_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pgroup_companies`
--

INSERT INTO `pgroup_companies` (`id`, `pgroup_id`, `company_id`) VALUES
(1, 1, 1),
(2, 2, 1);

--
-- Triggers `pgroup_companies`
--
DROP TRIGGER IF EXISTS `zPgroupCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zPgroupCompanyBfInsert` BEFORE INSERT ON `pgroup_companies`
 FOR EACH ROW BEGIN
	IF NEW.pgroup_id = "" OR NEW.pgroup_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `places`
--
DROP TRIGGER IF EXISTS `zPlaceBfInsert`;
DELIMITER //
CREATE TRIGGER `zPlaceBfInsert` BEFORE INSERT ON `places`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `searchs` (`name`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'a14fefe391e89f1520501dd178d66995', 'Doctor', '2018-12-24 15:10:12', 5, '2018-12-24 15:23:06', 5, 1),
(2, '1165e4a706a5c189a4e5413287bbaa1d', 'Nurse', '2018-12-24 15:10:18', 5, '2018-12-24 15:10:18', NULL, 1),
(3, '30780cf131a859401e171ae6761a69d5', 'Cashier Account', '2019-02-11 10:54:09', 1, '2019-02-11 10:56:25', 1, 1),
(4, '83d788afe66ea904bc726caaa3175c4b', 'Cleaner', '2019-02-11 10:54:19', 1, '2019-02-11 10:54:19', NULL, 1),
(5, '9b54ba1f0d8f1f4ef87a7fa18d7b1a47', 'Support', '2019-05-03 16:13:19', 1, '2019-05-03 16:13:24', 1, 1);

--
-- Triggers `positions`
--
DROP TRIGGER IF EXISTS `zPositionBfInsert`;
DELIMITER //
CREATE TRIGGER `zPositionBfInsert` BEFORE INSERT ON `positions`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pos_pick_details`
--

CREATE TABLE IF NOT EXISTS `pos_pick_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(11) DEFAULT NULL,
  `sales_order_detail_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `location_id` (`location_id`),
  KEY `product_id` (`product_id`),
  KEY `sales_order_detail_id` (`sales_order_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pos_price_types`
--

CREATE TABLE IF NOT EXISTS `pos_price_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_search` (`company_id`,`price_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pos_price_types`
--

INSERT INTO `pos_price_types` (`id`, `company_id`, `price_type_id`, `created`, `created_by`, `is_active`) VALUES
(1, 1, 3, '2017-08-23 15:49:07', 1, 2),
(2, 1, 3, '2018-02-16 09:16:25', 1, 2),
(3, 1, 4, '2019-05-03 16:12:39', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `price_types`
--

CREATE TABLE IF NOT EXISTS `price_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `is_set` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` tinyint(4) DEFAULT NULL,
  `is_ecommerce` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`),
  KEY `is_set` (`is_set`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `price_types`
--

INSERT INTO `price_types` (`id`, `sys_code`, `name`, `ordering`, `is_set`, `created`, `created_by`, `modified`, `modified_by`, `is_ecommerce`, `is_active`) VALUES
(1, '1fdc275e4fd22798c821a3098d56c69c', 'E-Commerce', 0, 1, '2017-03-23 20:09:35', 1, '2017-03-25 09:24:32', 1, 1, 1),
(2, '7d35f435976aedd4f536cb5e43a2b434', 'Standard Price', 1, 1, '2017-07-21 10:53:30', 1, '2023-05-31 16:15:26', 1, 0, 1),
(3, '56b444f1a5680d214249472b24fbe1b3', 'International Price', 2, 1, '2017-07-21 10:53:46', 1, '2023-05-31 16:15:35', 1, 0, 1),
(4, '3a82e6a80a6d972862873b8a20777cc6', 'S-price', 3, 1, '2019-05-03 16:12:39', 1, '2019-05-03 16:12:39', NULL, 0, 1);

--
-- Triggers `price_types`
--
DROP TRIGGER IF EXISTS `zPriceType`;
DELIMITER //
CREATE TRIGGER `zPriceType` BEFORE DELETE ON `price_types`
 FOR EACH ROW BEGIN
	IF OLD.id = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete this price type';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zPriceTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zPriceTypeBfInsert` BEFORE INSERT ON `price_types`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.ordering = "" OR NEW.ordering = NULL OR NEW.is_set = "" OR NEW.is_set = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `price_type_companies`
--

CREATE TABLE IF NOT EXISTS `price_type_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`),
  KEY `price_type_id` (`price_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `price_type_companies`
--

INSERT INTO `price_type_companies` (`id`, `company_id`, `price_type_id`) VALUES
(1, 1, 1),
(4, 1, 2),
(5, 1, 3),
(6, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE IF NOT EXISTS `printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `printer_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `silent` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`module_name`,`printer_name`),
  KEY `filters` (`module_name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`id`, `module_name`, `printer_name`, `silent`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '', 'POS', 0, '2017-12-22 13:31:46', 1, '2017-12-22 13:31:46', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `photo` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_kh` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chemical` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spec` text COLLATE utf8_unicode_ci,
  `color_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `default_cost` decimal(18,9) NOT NULL DEFAULT '0.000000000',
  `unit_cost` decimal(18,9) NOT NULL DEFAULT '0.000000000',
  `price_uom_id` int(11) DEFAULT NULL,
  `small_val_uom` int(11) NOT NULL DEFAULT '1',
  `width` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `length` double DEFAULT NULL,
  `size_uom_id` int(11) DEFAULT NULL,
  `cubic_meter` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `weight_uom_id` int(11) DEFAULT NULL,
  `reorder_level` double DEFAULT NULL,
  `period_from` date DEFAULT NULL,
  `period_to` date DEFAULT NULL,
  `file_catalog` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '1: New; 2: Used',
  `is_expired_date` tinyint(4) NOT NULL DEFAULT '0',
  `is_not_for_sale` tinyint(4) NOT NULL DEFAULT '0',
  `is_packet` tinyint(4) NOT NULL DEFAULT '0',
  `is_lots` tinyint(4) NOT NULL DEFAULT '0',
  `is_warranty` tinyint(4) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`code`,`barcode`,`name`,`name_kh`),
  KEY `filters` (`company_id`,`is_active`),
  KEY `type` (`type`,`is_expired_date`,`is_lots`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=60 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sys_code`, `parent_id`, `company_id`, `brand_id`, `photo`, `code`, `barcode`, `name`, `name_kh`, `chemical`, `spec`, `color_id`, `description`, `default_cost`, `unit_cost`, `price_uom_id`, `small_val_uom`, `width`, `height`, `length`, `size_uom_id`, `cubic_meter`, `weight`, `weight_uom_id`, `reorder_level`, `period_from`, `period_to`, `file_catalog`, `created`, `created_by`, `modified`, `modified_by`, `type`, `is_expired_date`, `is_not_for_sale`, `is_packet`, `is_lots`, `is_warranty`, `is_active`) VALUES
(1, '49e5cea9ceac2bf802725005a36cb827', NULL, 1, 1, NULL, 'PK001', 'PK001', 'Appetine (2mg/5ml) ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '30.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(2, '7cd60dfbdadaffe900e131bf6bf88735', NULL, 1, 1, NULL, 'PK002', 'PK002', 'AdvilMed(20mg/mL) ', NULL, 'Ibuprofen', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(3, 'fb6e0f82d08b44bae26fca0d34ef594f', NULL, 1, 1, NULL, 'PK003', 'PK003', 'AMK sp ', NULL, 'Amoxicillin/Clavulanic Acid', NULL, NULL, ' ', '0.000000000', '0.500000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(4, '5d9af8f51f500dec17aafc47869da60e', NULL, 1, 1, NULL, 'PK004', 'PK004', 'Augmentin 625mg ', NULL, 'Amoxicillin/Clavulanic Acid', NULL, NULL, ' ', '0.000000000', '2.300000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(5, 'dc65e24d6f8e414a896d4c33b0a27ef3', NULL, 1, 1, NULL, 'PK005', 'PK005', 'Almex sp ', NULL, 'Albendazole', NULL, NULL, ' ', '0.000000000', '10.000000000', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(6, 'f416c2c21f4cea2f7d10a0d69d7ff78b', NULL, 1, 1, NULL, 'PK006', 'PK006', 'Alcohol pad Kenko ', NULL, 'Alcohol', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(7, '73504708d6dd90887af6d96e1969b323', NULL, 1, 1, NULL, 'PK007', 'PK007', 'Babycanyl SP ', NULL, 'Terbutalin sulfat/Guaifenesin', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(8, '6abf931d54c55c97fc266f30af96a0a0', NULL, 1, 1, NULL, 'PK008', 'PK008', ' Nutrigen bonidenti 18ml ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(9, 'b31e1c99231856430fe141bbe44bcf7a', NULL, 1, 1, NULL, 'PK009', 'PK009', 'Bepanthen ointment ', NULL, 'Dexpanthenol', NULL, NULL, ' ', '0.000000000', '0.000000000', 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(10, '92f636c35fe1a812e0732962d53cb44b', NULL, 1, 1, NULL, 'PK010', 'PK010', 'Bioflor sac 250mg ', NULL, 'Saccharomyces boulardii', NULL, NULL, ' ', '0.000000000', '1.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(11, 'a249dcc6e4b961778d9b750cd38c113a', NULL, 1, 1, NULL, 'PK011', 'PK011', 'Bisolvon 8mg ', NULL, 'Bromhexine HCl', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(12, 'ad4b26f69737ba0aadd181c48a8b6bf8', NULL, 1, 1, NULL, 'PK012', 'PK012', 'Bisolvon Kids (4mg/5mL) ', NULL, 'Bromhexine HCl', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(13, '880fd70448310420efed9ed2c1ce51c2', NULL, 1, 1, NULL, 'PK013', 'PK013', 'Candid B Cream 15g ', NULL, 'Clotrimazole/Beclometasone', NULL, NULL, ' ', '0.000000000', '0.000000000', 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(14, '1e2a45f8eac82cb9cdc00986930b4c39', NULL, 1, 1, NULL, 'PK014', 'PK014', 'Ciprolar-fc cream ', NULL, 'Ciprofloxacin, Clotrimazole', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(15, '4590e85df9c9aed17aec87d7eb564c4c', NULL, 1, 1, NULL, 'PK015', 'PK015', 'Clamoxyl 250mg Sachet ', NULL, 'Amoxicillin', NULL, NULL, ' ', '0.000000000', '2.000000000', 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(16, '6487d634db827966af7430f30ab367d2', NULL, 1, 1, NULL, 'PK016', 'PK016', 'Colicaid ', NULL, 'Simethicone', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(17, 'eba3d42380483c94116da2aa272de6d7', NULL, 1, 1, NULL, 'PK017', 'PK017', 'Daktarin Gel ', NULL, 'Miconazole nitrate', NULL, NULL, ' ', '0.000000000', '0.000000000', 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(18, '86ff936cce38ebaedf0cd5392a6480fe', NULL, 1, 1, NULL, 'PK018', 'PK018', 'Duphalac ', NULL, 'Lactulose', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(19, '64f9e4cf645c60850c378ab8a7d8fceb', NULL, 1, 1, NULL, 'PK019', 'PK019', 'Enterogermina ', NULL, 'Engerogermia', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(20, '370c2167926992cde5bf0387c96931bd', NULL, 1, 1, NULL, 'PK020', 'PK020', 'Erythromycin 250mg ', NULL, 'Erythromycin', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(21, '446c924e4277332d03659a226c07a6e7', NULL, 1, 1, NULL, 'PK021', 'PK021', 'EOSINE Cooper ', NULL, 'eosine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(22, '5df7260ac3e209633364a0f0a3d025a5', NULL, 1, 1, NULL, 'PK022', 'PK022', 'Ferrovit ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(23, '79db4f2acf548592a9fd7405532adff2', NULL, 1, 1, NULL, 'PK023', 'PK023', 'Flemex SP ', NULL, 'Carbocysteine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(24, 'c751fd90948b6a151307a7b2d76245aa', NULL, 1, 1, NULL, 'PK024', 'PK024', 'Gaviscon (10ml/sachet ) ', NULL, 'Gaviscon', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(25, 'af00a7200d5b14deddeca8bb1417d31b', NULL, 1, 1, NULL, 'PK025', 'PK025', 'Microlax bébé ', NULL, 'Sorbitol-citrate', NULL, NULL, ' ', '0.000000000', '0.000000000', 6, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(26, '5fc1320b8f4e97bb9ad64614eeb017a0', NULL, 1, 1, NULL, 'PK026', 'PK026', 'Oral Aid ', NULL, 'Oral aid', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(27, '7780110b7e237b6928615d9c7bcd8958', NULL, 1, 1, NULL, 'PK027', 'PK027', 'Osmolax ', NULL, 'Osmolax', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(28, 'ca4924dd72799e1674e1cda19795c6d0', NULL, 1, 1, NULL, 'PK028', 'PK028', 'Ostelin Kids Vitamin D3 Liquid 20ml ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '1.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(29, '7d12d4efa12c92988d2128eef76ba98c', NULL, 1, 1, NULL, 'PK029', 'PK029', 'OTOFA (Ear drop) ', NULL, 'Rifamycin', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(30, '29ce63e6c95b31b4e3713025b8d249d5', NULL, 1, 1, NULL, 'PK030', 'PK030', 'Otrivin (Nasal drop) ', NULL, 'Xylometazoline', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(31, '79c584d56beccd6b622dfca47395608e', NULL, 1, 1, NULL, 'PK031', 'PK031', 'Pediakid Vitamin D3 1000UI ', NULL, 'Vitamine ', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(32, '1e9c2c18a35ff89a72297db26e7cf7b2', NULL, 1, 1, NULL, 'PK032', 'PK032', 'Phénergan sp ', NULL, 'Promethazine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(33, '8a23f7c9712c27071bed60bd82e130e3', NULL, 1, 1, NULL, 'PK033', 'PK033', 'Physiodose ', NULL, 'NSS', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(34, 'd4bdd120f834b0e4033ded5c42e4267a', NULL, 1, 1, NULL, 'PK034', 'PK034', 'Refresh tears ', NULL, 'Refresh tears', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(35, 'bb1930d07eb5ffa2aed123ad46bde00d', NULL, 1, 1, NULL, 'PK035', 'PK035', 'Royal D ', NULL, 'Royal D', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(36, 'ea3399a5e92cb5d20762a05d2ce49331', NULL, 1, 1, NULL, 'PK036', 'PK036', 'Smecta ', NULL, 'Diosmectite', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(37, '5dba247633ea8d60473df1317f83d319', NULL, 1, 1, NULL, 'PK037', 'PK037', 'Square Zinc 20mg ', NULL, 'Zinc sulfate Monohydrate ', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(38, '999dad488d2069687412a7430165819b', NULL, 1, 1, NULL, 'PK038', 'PK038', 'Starcef 200mg ', NULL, 'Cefixime', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(39, '5748ecfd821219d2e9d59548dea301f8', NULL, 1, 1, NULL, 'PK039', 'PK039', 'Stérimer ', NULL, 'Stérimar', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(40, 'f10346b80d3ee263f5b458e7da008e7d', NULL, 1, 1, NULL, 'PK040', 'PK040', 'Sudocream ', NULL, 'Zinc oxide', NULL, NULL, ' ', '0.000000000', '0.000000000', 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(41, '42b91ed285f68af2c4cc0e7987c4f7fe', NULL, 1, 1, NULL, 'PK041', 'PK041', 'Tiorfan ', NULL, 'Racécadotril', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(42, 'a46f49c7743cdc9f76338c4e99250e4c', NULL, 1, 1, NULL, 'PK042', 'PK042', 'Tobradex ', NULL, 'Tobramycin, Dexamethasone', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(43, '5998a3fab1d841125f86e1bd221bf405', NULL, 1, 1, NULL, 'PK043', 'PK043', 'Tobrex ', NULL, 'Tobramycin', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(44, 'bd7f2fdf1a4125bd0ec81111243230ef', NULL, 1, 1, NULL, 'PK044', 'PK044', 'Vomena ', NULL, 'Ondansetron', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(45, 'a976501ce7d5c135fc6d4386dee5ab71', NULL, 1, 1, NULL, 'PK045', 'PK045', 'Wellbaby liquid (4m-4y) ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(46, '0c5a7a32b8a6776679a6372af5d31565', NULL, 1, 1, NULL, 'PK046', 'PK046', 'Zenlee Plus ', NULL, 'Albendazole + Ivermectin', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(47, '95ddf09f3553912298463459e32c12e7', NULL, 1, 1, NULL, 'PK047', 'PK047', 'x ', NULL, 'Albendazole ', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(48, '9c3b0cba92fea4c66717902eab2bbefe', NULL, 1, 1, NULL, 'PK048', 'PK048', 'Zymaduo 300UI ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(49, 'f2bde1a834afea6f7c50d3feb7ffc4e0', NULL, 1, 1, NULL, 'PK049', 'PK049', 'Zyrtec Syrup ', NULL, 'Cetirizine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(50, 'd7c0fb82c57fc9ae96e50155fcca05c2', NULL, 1, 1, NULL, 'PK050', 'PK050', 'Aldacton 25mg ', NULL, 'Spironolactone', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(51, 'cc93dc86914c19f4d0910fe2dd6a1b7f', NULL, 1, 1, NULL, 'PK051', 'PK051', 'Lasilix 40mg ', NULL, 'Furosemide', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(52, 'cbccc51501f4f4da81020512a051420f', NULL, 1, 1, NULL, 'PK052', 'PK052', 'Lopril25mg ', NULL, 'Captopril', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(53, 'a3f54cc82d1f1f56197dcd2b7448981a', NULL, 1, 1, NULL, 'PK053', 'PK053', 'Avlocardyl40mg ', NULL, 'Propranolol', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(54, '4412281b779ac59136d60b64fd875771', NULL, 1, 1, NULL, 'PK054', 'PK054', 'Adalate 20mg ', NULL, 'Nifedipine', NULL, NULL, ' ', '0.000000000', '0.000000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(55, 'b93fedab46fcb29459a2890d3e97fbf1', NULL, 1, 1, NULL, 'PK055', 'PK055', 'B1 B6 B12 ', NULL, 'Vitamine', NULL, NULL, ' ', '0.000000000', '0.000000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(56, '798d3e4f33ddb773bc5bcf4a6dbab3e3', NULL, 1, 1, NULL, 'PK056', 'PK056', 'Bedelix ', NULL, 'Montmorillonite beidellitique 3g', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(57, 'e6eb15bc53ad47933e59c7f71058560d', NULL, 1, 1, NULL, 'PK057', 'PK057', 'Actapulgite ', NULL, 'Attagulgite de Mormoiron activée', NULL, NULL, ' ', '0.000000000', '0.000000000', 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-09 14:50:28', 1, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(58, 'd2cf4adaf05bed93a576d7084edcb1a0', NULL, 1, 1, '', 'V0001', 'V0001', 'វ៉ាក់សាំងតេតាណុស', NULL, 'វ៉ាក់សាំងតេតាណុស', '', NULL, '', '0.500000000', '0.500000000', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2023-02-24 10:07:58', 1, '2023-02-24 10:07:58', NULL, 1, 0, 0, 0, 0, 0, 1),
(59, '8c9dcadca848cb65bc02d2f46ebf590b', NULL, 1, 1, '', 'Pana500', 'Pana500', 'Panadol 500mg', NULL, 'DHA', '', NULL, '', '2.000000000', '1.900000000', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, NULL, NULL, NULL, '2023-05-31 15:57:07', 1, '2023-05-31 15:57:07', NULL, 1, 0, 0, 0, 0, 0, 1);

--
-- Triggers `products`
--
DROP TRIGGER IF EXISTS `zProductAfInsert`;
DELIMITER //
CREATE TRIGGER `zProductAfInsert` AFTER INSERT ON `products`
 FOR EACH ROW BEGIN
	UPDATE cache_datas SET modified = now() WHERE `type` = 'Products';
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zProductAfUpdate`;
DELIMITER //
CREATE TRIGGER `zProductAfUpdate` AFTER UPDATE ON `products`
 FOR EACH ROW BEGIN
	UPDATE cache_datas SET modified = now() WHERE `type` = 'Products';
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zProductBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductBfInsert` BEFORE INSERT ON `products`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code IS NULL OR NEW.company_id = "" OR NEW.company_id IS NULL OR NEW.code = "" OR NEW.code IS NULL OR NEW.barcode = "" OR NEW.barcode IS NULL OR NEW.name = "" OR NEW.name IS NULL OR NEW.unit_cost IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zProductBfUpdate`;
DELIMITER //
CREATE TRIGGER `zProductBfUpdate` BEFORE UPDATE ON `products`
 FOR EACH ROW BEGIN
	DECLARE isCheck TINYINT(4);
	IF OLD.is_active = 1 AND NEW.is_active =2 THEN
		SELECT SUM(total_qty) INTO isCheck FROM inventory_totals WHERE product_id = OLD.id GROUP BY product_id;
		IF isCheck > 0 THEN 
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Data cloud not been delete';
		END IF;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_branches`
--

CREATE TABLE IF NOT EXISTS `product_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=499 ;

--
-- Dumping data for table `product_branches`
--

INSERT INTO `product_branches` (`id`, `product_id`, `branch_id`) VALUES
(85, 85, 1),
(89, 89, 1),
(121, 78, 1),
(132, 68, 1),
(149, 71, 1),
(151, 62, 1),
(153, 36, 1),
(154, 33, 1),
(155, 32, 1),
(157, 87, 1),
(159, 59, 1),
(160, 86, 1),
(164, 88, 1),
(170, 70, 1),
(197, 73, 1),
(202, 42, 1),
(205, 77, 1),
(208, 21, 1),
(210, 91, 1),
(211, 50, 1),
(222, 111, 1),
(223, 90, 1),
(227, 9, 1),
(237, 61, 1),
(243, 82, 1),
(248, 108, 1),
(266, 93, 1),
(268, 48, 1),
(269, 58, 1),
(274, 38, 1),
(275, 92, 1),
(276, 37, 1),
(281, 106, 1),
(287, 79, 1),
(299, 20, 1),
(300, 19, 1),
(307, 65, 1),
(321, 67, 1),
(323, 16, 1),
(324, 40, 1),
(325, 15, 1),
(326, 41, 1),
(329, 17, 1),
(331, 69, 1),
(332, 95, 1),
(333, 84, 1),
(341, 45, 1),
(342, 18, 1),
(349, 57, 1),
(350, 24, 1),
(351, 112, 1),
(354, 53, 1),
(356, 56, 1),
(358, 23, 1),
(362, 13, 1),
(363, 5, 1),
(364, 12, 1),
(365, 3, 1),
(366, 10, 1),
(367, 11, 1),
(368, 8, 1),
(370, 2, 1),
(371, 83, 1),
(372, 72, 1),
(373, 60, 1),
(374, 63, 1),
(375, 43, 1),
(377, 4, 1),
(378, 31, 1),
(379, 39, 1),
(382, 25, 1),
(383, 26, 1),
(384, 46, 1),
(385, 6, 1),
(387, 34, 1),
(389, 105, 1),
(390, 109, 1),
(391, 102, 1),
(392, 104, 1),
(393, 103, 1),
(394, 96, 1),
(395, 101, 1),
(396, 100, 1),
(398, 99, 1),
(399, 107, 1),
(403, 35, 1),
(404, 115, 1),
(405, 114, 1),
(406, 116, 1),
(407, 117, 1),
(408, 118, 1),
(409, 94, 1),
(410, 97, 1),
(412, 98, 1),
(413, 113, 1),
(419, 7, 1),
(421, 44, 1),
(422, 1, 1),
(424, 66, 1),
(425, 47, 1),
(426, 110, 1),
(428, 51, 1),
(429, 52, 1),
(430, 27, 1),
(431, 30, 1),
(432, 54, 1),
(433, 55, 1),
(434, 49, 1),
(435, 64, 1),
(436, 29, 1),
(437, 28, 1),
(440, 120, 1),
(444, 80, 1),
(445, 75, 1),
(446, 122, 1),
(447, 121, 1),
(449, 123, 1),
(451, 125, 1),
(452, 124, 1),
(455, 22, 1),
(456, 127, 1),
(458, 128, 1),
(460, 130, 1),
(461, 14, 1),
(464, 119, 1),
(466, 131, 1),
(473, 132, 1),
(474, 133, 1),
(481, 134, 1),
(482, 81, 1),
(483, 135, 1),
(484, 136, 1),
(485, 76, 1),
(486, 137, 1),
(487, 74, 1),
(489, 139, 1),
(491, 138, 1),
(492, 129, 1),
(493, 140, 1),
(494, 141, 1),
(496, 126, 1),
(497, 58, 1),
(498, 59, 1);

--
-- Triggers `product_branches`
--
DROP TRIGGER IF EXISTS `zProductBranchBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductBranchBfInsert` BEFORE INSERT ON `product_branches`
 FOR EACH ROW BEGIN
	IF NEW.product_id = "" OR NEW.product_id = NULL OR NEW.branch_id = "" OR NEW.branch_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE IF NOT EXISTS `product_inventories` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `location_group_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `expired_date` date NOT NULL,
  `total_qty` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`product_id`,`location_group_id`,`location_id`,`lots_number`,`expired_date`),
  KEY `products` (`product_id`,`location_group_id`,`location_id`,`lots_number`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_inventories`
--

INSERT INTO `product_inventories` (`product_id`, `location_group_id`, `location_id`, `lots_number`, `expired_date`, `total_qty`) VALUES
(1, 0, 1, '0', '0000-00-00', '100.000'),
(1, 1, 1, '0', '0000-00-00', '999955.000'),
(2, 1, 1, '0', '0000-00-00', '999348.000'),
(3, 1, 1, '0', '0000-00-00', '997874.000'),
(4, 1, 1, '0', '0000-00-00', '998287.000'),
(5, 1, 1, '0', '0000-00-00', '999747.000'),
(6, 1, 1, '0', '0000-00-00', '999790.000'),
(7, 1, 1, '0', '0000-00-00', '999942.000'),
(8, 1, 1, '0', '0000-00-00', '999805.000'),
(9, 1, 1, '0', '0000-00-00', '999977.000'),
(10, 1, 1, '0', '0000-00-00', '999382.000'),
(11, 1, 1, '0', '0000-00-00', '999867.000'),
(12, 1, 1, '0', '0000-00-00', '1000000.000'),
(13, 1, 1, '0', '0000-00-00', '999605.000'),
(14, 1, 1, '0', '0000-00-00', '999309.000'),
(15, 1, 1, '0', '0000-00-00', '999974.000'),
(16, 1, 1, '0', '0000-00-00', '999886.000'),
(17, 1, 1, '0', '0000-00-00', '999920.000'),
(18, 1, 1, '0', '0000-00-00', '997918.000'),
(19, 1, 1, '0', '0000-00-00', '999964.000'),
(20, 1, 1, '0', '0000-00-00', '999879.000'),
(21, 1, 1, '0', '0000-00-00', '999789.000'),
(22, 1, 1, '0', '0000-00-00', '999897.000'),
(23, 1, 1, '0', '0000-00-00', '999261.000'),
(24, 1, 1, '0', '0000-00-00', '99960.000'),
(25, 1, 1, '0', '0000-00-00', '999984.000'),
(26, 1, 1, '0', '0000-00-00', '99966.000'),
(27, 1, 1, '0', '0000-00-00', '999862.000'),
(28, 0, 0, '0', '0000-00-00', '500.000'),
(28, 1, 1, '0', '0000-00-00', '100469.000'),
(28, 1, 1, '0', '1970-01-01', '-20.000'),
(29, 1, 1, '0', '0000-00-00', '999959.000'),
(30, 1, 1, '0', '0000-00-00', '4.000'),
(31, 1, 1, '0', '0000-00-00', '999964.000'),
(32, 1, 1, '0', '0000-00-00', '1000000.000'),
(33, 1, 1, '0', '0000-00-00', '999886.000'),
(34, 1, 1, '0', '0000-00-00', '999931.000'),
(35, 1, 1, '0', '0000-00-00', '999708.000'),
(36, 1, 1, '0', '0000-00-00', '999983.000'),
(37, 1, 1, '0', '0000-00-00', '996316.000'),
(38, 1, 1, '0', '0000-00-00', '99880.000'),
(39, 1, 1, '0', '0000-00-00', '999980.000'),
(40, 1, 1, '0', '0000-00-00', '999923.000'),
(41, 1, 1, '0', '0000-00-00', '999972.000'),
(42, 1, 1, '0', '0000-00-00', '999864.000'),
(43, 1, 1, '0', '0000-00-00', '999967.000'),
(44, 1, 1, '0', '0000-00-00', '999923.000'),
(45, 1, 1, '0', '0000-00-00', '99971.000'),
(46, 1, 1, '0', '0000-00-00', '999922.000'),
(47, 1, 1, '0', '0000-00-00', '999695.000'),
(48, 1, 1, '0', '0000-00-00', '999717.000'),
(49, 1, 1, '0', '0000-00-00', '999612.000'),
(50, 1, 1, '0', '0000-00-00', '999981.000'),
(51, 1, 1, '0', '0000-00-00', '999977.000'),
(52, 1, 1, '0', '0000-00-00', '9905.000'),
(53, 1, 1, '0', '0000-00-00', '999811.000'),
(54, 1, 1, '0', '0000-00-00', '999958.000'),
(55, 1, 1, '0', '0000-00-00', '999887.000'),
(56, 1, 1, '0', '0000-00-00', '998089.000'),
(57, 1, 1, '0', '0000-00-00', '999828.000'),
(58, 1, 1, '0', '0000-00-00', '999437.000'),
(59, 1, 1, '0', '0000-00-00', '999530.000'),
(60, 1, 1, '0', '0000-00-00', '999981.000'),
(61, 1, 1, '0', '0000-00-00', '999942.000'),
(62, 1, 1, '0', '0000-00-00', '999912.000'),
(63, 1, 1, '0', '0000-00-00', '999996.000'),
(64, 1, 1, '0', '0000-00-00', '9714.000'),
(65, 1, 1, '0', '0000-00-00', '997908.000'),
(66, 1, 1, '0', '0000-00-00', '999985.000'),
(67, 1, 1, '0', '0000-00-00', '9989.000'),
(68, 1, 1, '0', '0000-00-00', '999990.000'),
(69, 1, 1, '0', '0000-00-00', '999976.000'),
(70, 1, 1, '0', '0000-00-00', '999990.000'),
(71, 1, 1, '0', '0000-00-00', '999967.000'),
(72, 1, 1, '0', '0000-00-00', '999280.000'),
(73, 1, 1, '0', '0000-00-00', '999992.000'),
(74, 1, 1, '0', '0000-00-00', '1999996.000'),
(75, 1, 1, '0', '0000-00-00', '999974.000'),
(76, 1, 1, '0', '0000-00-00', '999976.000'),
(77, 1, 1, '0', '0000-00-00', '999999.000'),
(78, 1, 1, '0', '0000-00-00', '9977.000'),
(79, 1, 1, '0', '0000-00-00', '999990.000'),
(80, 1, 1, '0', '0000-00-00', '1000000.000'),
(81, 1, 1, '0', '0000-00-00', '999912.000'),
(82, 1, 1, '0', '0000-00-00', '999970.000'),
(83, 1, 1, '0', '0000-00-00', '1000000.000'),
(84, 1, 1, '0', '0000-00-00', '999960.000'),
(85, 1, 1, '0', '0000-00-00', '999933.000'),
(86, 1, 1, '0', '0000-00-00', '999973.000'),
(87, 1, 1, '0', '0000-00-00', '999999.000'),
(88, 1, 1, '0', '0000-00-00', '1000000.000'),
(89, 1, 1, '0', '0000-00-00', '1000000.000'),
(90, 1, 1, '0', '0000-00-00', '999992.000'),
(91, 1, 1, '0', '0000-00-00', '1000000.000'),
(92, 1, 1, '0', '0000-00-00', '1999999.000'),
(93, 1, 1, '0', '0000-00-00', '999954.000'),
(94, 1, 1, '0', '0000-00-00', '999712.000'),
(94, 1, 1, '0', '2020-02-01', '0.000'),
(94, 1, 1, '0', '2022-02-01', '0.000'),
(95, 1, 1, '0', '0000-00-00', '999934.000'),
(96, 1, 1, '0', '0000-00-00', '999865.000'),
(97, 1, 1, '0', '0000-00-00', '999636.000'),
(98, 1, 1, '0', '0000-00-00', '999809.000'),
(99, 1, 1, '0', '0000-00-00', '999672.000'),
(100, 1, 0, '0', '0000-00-00', '1000000.000'),
(100, 1, 1, '0', '0000-00-00', '999715.000'),
(101, 1, 1, '0', '0000-00-00', '999940.000'),
(102, 1, 1, '0', '0000-00-00', '999483.000'),
(103, 1, 1, '0', '0000-00-00', '999995.000'),
(104, 1, 1, '0', '0000-00-00', '999799.000'),
(105, 1, 1, '0', '0000-00-00', '999470.000'),
(106, 1, 1, '0', '0000-00-00', '999986.000'),
(107, 1, 1, '0', '0000-00-00', '999985.000'),
(108, 1, 1, '0', '0000-00-00', '999990.000'),
(109, 1, 1, '0', '0000-00-00', '100000.000'),
(110, 1, 1, '0', '0000-00-00', '1000000.000'),
(111, 1, 1, '0', '0000-00-00', '997627.000'),
(113, 1, 1, '0', '0000-00-00', '996462.000'),
(114, 1, 1, '0', '0000-00-00', '999998.000'),
(115, 1, 1, '0', '0000-00-00', '999724.000'),
(116, 1, 1, '0', '0000-00-00', '999956.000'),
(117, 1, 1, '0', '0000-00-00', '1000000.000'),
(117, 2, 2, '0', '0000-00-00', '0.000'),
(118, 1, 1, '0', '0000-00-00', '999958.000'),
(119, 1, 1, '0', '0000-00-00', '999951.000'),
(120, 1, 1, '0', '0000-00-00', '1000000.000'),
(121, 1, 1, '0', '0000-00-00', '1000000.000'),
(122, 1, 1, '0', '0000-00-00', '1000000.000'),
(123, 1, 1, '0', '0000-00-00', '999956.000'),
(124, 1, 1, '0', '0000-00-00', '1000.000'),
(125, 1, 1, '0', '0000-00-00', '999.000'),
(126, 1, 1, '0', '0000-00-00', '999623.000'),
(127, 1, 1, '0', '0000-00-00', '999991.000'),
(128, 1, 1, '0', '0000-00-00', '999837.000'),
(129, 1, 1, '0', '0000-00-00', '999873.000'),
(130, 1, 1, '0', '0000-00-00', '999998.000'),
(131, 1, 1, '0', '0000-00-00', '987.000'),
(132, 1, 1, '0', '0000-00-00', '9982.000'),
(133, 1, 1, '0', '0000-00-00', '999987.000'),
(134, 1, 1, '0', '0000-00-00', '1000820.000'),
(135, 1, 1, '0', '0000-00-00', '999979.000'),
(136, 1, 1, '0', '0000-00-00', '999979.000'),
(137, 1, 1, '0', '0000-00-00', '999843.000'),
(138, 1, 1, '0', '0000-00-00', '9999790.000'),
(139, 1, 1, '0', '0000-00-00', '999978.000'),
(140, 1, 1, '0', '0000-00-00', '999957.000'),
(141, 1, 1, '0', '0000-00-00', '999964.000');

-- --------------------------------------------------------

--
-- Table structure for table `product_pgroups`
--

CREATE TABLE IF NOT EXISTS `product_pgroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) DEFAULT NULL,
  `pgroup_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id_pgroup_id` (`product_id`,`pgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=60 ;

--
-- Dumping data for table `product_pgroups`
--

INSERT INTO `product_pgroups` (`id`, `product_id`, `pgroup_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1),
(20, 20, 1),
(21, 21, 1),
(22, 22, 1),
(23, 23, 1),
(24, 24, 1),
(25, 25, 1),
(26, 26, 1),
(27, 27, 1),
(28, 28, 1),
(29, 29, 1),
(30, 30, 1),
(31, 31, 1),
(32, 32, 1),
(33, 33, 1),
(34, 34, 1),
(35, 35, 1),
(36, 36, 1),
(37, 37, 1),
(38, 38, 1),
(39, 39, 1),
(40, 40, 1),
(41, 41, 1),
(42, 42, 1),
(43, 43, 1),
(44, 44, 1),
(45, 45, 1),
(46, 46, 1),
(47, 47, 1),
(48, 48, 1),
(49, 49, 1),
(50, 50, 1),
(51, 51, 1),
(52, 52, 1),
(53, 53, 1),
(54, 54, 1),
(55, 55, 1),
(56, 56, 1),
(57, 57, 1),
(58, 58, 2),
(59, 59, 1);

--
-- Triggers `product_pgroups`
--
DROP TRIGGER IF EXISTS `zProductPgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductPgroupBfInsert` BEFORE INSERT ON `product_pgroups`
 FOR EACH ROW BEGIN
	IF NEW.product_id = "" OR NEW.product_id = NULL OR NEW.pgroup_id = "" OR NEW.pgroup_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_photos`
--

CREATE TABLE IF NOT EXISTS `product_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_prices`
--

CREATE TABLE IF NOT EXISTS `product_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `product_id` bigint(20) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `old_unit_cost` decimal(15,3) DEFAULT '0.000',
  `amount_before` decimal(15,3) DEFAULT '0.000',
  `amount` decimal(15,3) DEFAULT '0.000',
  `percent` decimal(6,3) DEFAULT '0.000',
  `add_on` decimal(15,3) DEFAULT '0.000',
  `set_type` tinyint(4) DEFAULT '0' COMMENT '1: Amount, 2: Percent, 3: Add On',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `uom_id` (`uom_id`),
  KEY `price_type_id` (`price_type_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=175 ;

--
-- Dumping data for table `product_prices`
--

INSERT INTO `product_prices` (`id`, `sys_code`, `branch_id`, `product_id`, `price_type_id`, `uom_id`, `old_unit_cost`, `amount_before`, `amount`, `percent`, `add_on`, `set_type`, `created`, `created_by`) VALUES
(1, '6b34e40b9d73524f63bdbe2a6d0a024e', 1, 1, 2, 1, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(2, 'efa37149845ae55acebfb79b0397e1f7', 1, 1, 3, 1, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(3, 'a8de524eb89e6756efe393eb4bf55bd3', 1, 1, 4, 1, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(4, 'dbd90f727e776280a2d7c78607976018', 1, 2, 2, 1, '0.000', '0.000', '7.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(5, '38af648ddda075c470cf2e48e9ae6626', 1, 2, 3, 1, '0.000', '0.000', '7.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(6, '74bbac21d526d5c3765376af7577d7ad', 1, 2, 4, 1, '0.000', '0.000', '7.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(7, '4954fc32d92266bc880abf4f77ab9340', 1, 3, 2, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(8, '3620a441979b8af2fa601d928ed0259c', 1, 3, 3, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(9, '2474d0f141ca73ab851272ee9de718f9', 1, 3, 4, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(10, 'f6d9f8f1a626abddb071d72e4743dc7e', 1, 4, 2, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(11, '30ad7f081c9ab777b7d6194e98b9f8d9', 1, 4, 3, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(12, '281f5b0328800790dae8ded8186c41e5', 1, 4, 4, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(13, '01c473182e7b907272d575ad6bb11552', 1, 5, 2, 3, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(14, '90aa5bbf408ff287d3c72a932b04800d', 1, 5, 3, 3, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(15, 'b4cff0bc8165adeffbf4797bca9e1f31', 1, 5, 4, 3, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(16, '4f8f8effe6d2642b5086eb12f1783e9e', 1, 6, 2, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(17, 'e0a08866613aacaa25ccf9717cd990f7', 1, 6, 3, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(18, '9cccaff32d09b47d58937d01482857e9', 1, 6, 4, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(19, '59b913afa9ba6fbb241883266f387767', 1, 7, 2, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(20, 'ff9b171563060b925254a42eae4717c9', 1, 7, 3, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(21, 'a0399f1d56a0652a0711cd161993391d', 1, 7, 4, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(22, 'fb43b86bc9523b5adb8e2fa6f369b1ef', 1, 8, 2, 1, '0.000', '0.000', '7.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(23, '4b37d44c0e006a3da62278959d9b53b5', 1, 8, 3, 1, '0.000', '0.000', '7.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(24, 'a420cc95c47048c4ff1f398ea8acbc46', 1, 8, 4, 1, '0.000', '0.000', '7.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(25, '3035bb5c33d86fccb90ec301f0c2b3ce', 1, 9, 2, 4, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(26, 'daa04b0bef00591f92e78251daf6b0eb', 1, 9, 3, 4, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(27, 'b8425d3f5fe90ba37343bda049344f26', 1, 9, 4, 4, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(28, '5b4cbf80eac77449919a5dee27647cad', 1, 10, 2, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(29, 'b436599caead2b48ed71f392e56cd5ba', 1, 10, 3, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(30, '3227dd18090de2f3c7a91935411f7a93', 1, 10, 4, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(31, 'e7adc5d286586e4bc4b1054db2e7cebf', 1, 11, 2, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(32, '344a3af34dc5c286e60bed7729262565', 1, 11, 3, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(33, '9527787ccc7476cbf40114d11d6ecfcc', 1, 11, 4, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(34, 'fe56f25c3e6ede4390c7270178a3ae8f', 1, 12, 2, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(35, '96782ee5e8e89e2e3820ace60cc7e57d', 1, 12, 3, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(36, '96fa85fee55d29e1fd2e8e97eb151614', 1, 12, 4, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(37, 'a7148395cfc79f84d5a220d886e63028', 1, 13, 2, 4, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(38, '4ca2e7642bcb158d2c16300173bc8dc7', 1, 13, 3, 4, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(39, '26d99433fd2f37557bdb987a8761a200', 1, 13, 4, 4, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(40, '929d554a2377bec5c90f7da0fc5f0edc', 1, 14, 2, 5, '0.000', '0.000', '1.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(41, 'f4d7ac6a5a61f6f0ce8450c799d75067', 1, 14, 3, 5, '0.000', '0.000', '1.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(42, '172e46b856e118324255dcd339af81aa', 1, 14, 4, 5, '0.000', '0.000', '1.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(43, 'e20d437f53879a72d33889f95c7b92e5', 1, 15, 2, 4, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(44, '92184c679c1fa31ed9a605da56a06489', 1, 15, 3, 4, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(45, 'dab1c8cd83e351f40da61973ecec9d03', 1, 15, 4, 4, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(46, '6c15185a8e59914ac6037e5d486596b1', 1, 16, 2, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(47, '27ad84ce6b1b13cda7c9f91bd4eb37cb', 1, 16, 3, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(48, '829d7970e6ddc142e3e01ab43549497b', 1, 16, 4, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(49, '140063361b7673c835b2626ea9002d69', 1, 17, 2, 4, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(50, '8a5699d7ba087eb12acdc39691db967f', 1, 17, 3, 4, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(51, 'b7477699a638b86dfb76f250bc859c77', 1, 17, 4, 4, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(52, '6113f091471e8fb5e75349015560f111', 1, 18, 2, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(53, 'ef127582f6e022ceb8784451a7eebd5c', 1, 18, 3, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(54, '24e4d8be75a3d7d9324e04cdbb738c41', 1, 18, 4, 5, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(55, 'c24dd3a1ca4e1c4b5d3a3684204278e0', 1, 19, 2, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(56, '15522d04f98d5f304a5e875347f684fd', 1, 19, 3, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(57, '8e7c56ef5e82ad997bf2da34b6417f02', 1, 19, 4, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(58, 'f9ef73da78114a444d04b3a6e098e25a', 1, 20, 2, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(59, '05f3fb8150b37ca69278cc6f870195b5', 1, 20, 3, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(60, '3dbf5eefc0f479cfa2dc051a6d06458a', 1, 20, 4, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(61, '68e9b1636f91dee82eb02a0cd990e237', 1, 21, 2, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(62, '30572a641fbe8c25de33d8bd03fbe263', 1, 21, 3, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(63, '673372bdc3555f212a085b3bd378b8e0', 1, 21, 4, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(64, '9e2fc41610388c6a1580d79ffb026579', 1, 22, 2, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(65, '3c20559870d12027a6834c1ece73fc9b', 1, 22, 3, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(66, '9335564a33eed7931c350b470947b70c', 1, 22, 4, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(67, '2b4e9e23e6c361d0dcffa7b203c97e38', 1, 23, 2, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(68, '3317e10c4c4144597cacf2fa031858bc', 1, 23, 3, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(69, '0864f71f2e4ce0f397570e7ce94823e7', 1, 23, 4, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(70, '335f6a0914dd87055c9e7f4c1b241970', 1, 24, 2, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(71, 'eb1745760a5fbb1cf055b07d3d80bba6', 1, 24, 3, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(72, 'bff718893377350da04c22e795050bdf', 1, 24, 4, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(73, '5c33288dfde564026ea7beebaa486c37', 1, 25, 2, 6, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(74, 'c618fd87ba62e171fce414e51ede2497', 1, 25, 3, 6, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(75, '7aafb292cb6af4e8d43216e3662c8e1a', 1, 25, 4, 6, '0.000', '0.000', '2.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(76, 'd6ee23a8bef5036134dd8f3cd000c120', 1, 26, 2, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(77, '5e74aecf3e19535e187b183986e29bb9', 1, 26, 3, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(78, 'f4cc0b0d11076cb3cc16f373f785232e', 1, 26, 4, 1, '0.000', '0.000', '2.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(79, 'e49c701f6674e1f779b10453c082436b', 1, 27, 2, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(80, '416f57f3ed4639ca655a1eb4de3ae4c1', 1, 27, 3, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(81, '7684d943d956eecacdab45ffa57ada02', 1, 27, 4, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(82, '04d96e90c1b6beb1a00d3bdc50c4d107', 1, 28, 2, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1),
(83, '2127c1556a54af4c571f8606493e2cd4', 1, 28, 3, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1),
(84, '108a00c819f068e5743d60075c2f2d74', 1, 28, 4, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1),
(85, 'fd67ea8679b4aeafd029f791beb0f2c3', 1, 29, 2, 1, '0.000', '0.000', '6.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(86, '45ec2e199b20b46d35c4cf86a151ad33', 1, 29, 3, 1, '0.000', '0.000', '6.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(87, '114086cbdb7dc6198d9cbb566ceb8b76', 1, 29, 4, 1, '0.000', '0.000', '6.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(88, 'f46e7ee4700fe4c6e312ee9b91b8ba54', 1, 30, 2, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(89, '3778770be0f6848c61177004dbd909ea', 1, 30, 3, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(90, '10f58ddb7c47dddff123d1afea70621e', 1, 30, 4, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(91, '6b3176cea9eedce4d20e4a3a300c823e', 1, 31, 2, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(92, '873c5e6441ca9181b001d7f6db758f69', 1, 31, 3, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(93, '54cf3e6e5075a310f2a78c049fe45e05', 1, 31, 4, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(94, 'ae30cde1c6118b8d1d001647d5947698', 1, 32, 2, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(95, '5e92b523c611a222bdc458e9b0957012', 1, 32, 3, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(96, '8ca56717dd63a5069c2a766317921c8d', 1, 32, 4, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(97, 'e64a14b0fc830107c62430bcec5a1bc8', 1, 33, 2, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(98, '30252d50b39d5d6923844c000ab4878a', 1, 33, 3, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(99, 'e84b466e988a4d4d93ba3a6b1c87d33d', 1, 33, 4, 1, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(100, '0d491e61b5780355096c1e38b77302d0', 1, 34, 2, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(101, 'f01a8feefffbe7c64b21f193402851f9', 1, 34, 3, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(102, '4c1bac4426863a6384002f8497cd634c', 1, 34, 4, 1, '0.000', '0.000', '4.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(103, 'b853120796ac8362ce4a1b99fc17cdaf', 1, 35, 2, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(104, '9f5cde55a2be5169dfe2786275c00fcc', 1, 35, 3, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(105, 'ba63eb6c1d115a75f705ad01ff11566c', 1, 35, 4, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(106, '17886518ef8075cf92b200808b8252ee', 1, 36, 2, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(107, 'b052bbe6cde613b15f6c642904826eec', 1, 36, 3, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(108, 'ee694cb1f82d426e28fae93f77451035', 1, 36, 4, 5, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(109, '28ed134d18cb93269a6faa1f407af8db', 1, 37, 2, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(110, '432c88d87b2167efd18a9c7f1928e17e', 1, 37, 3, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(111, '32989257b1a0ea6ad64e9da7689b70fa', 1, 37, 4, 2, '0.000', '0.000', '0.250', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(112, '2389c8f21fd93223506e25a87fa6c41d', 1, 38, 2, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(113, '16c1957934265cce5011d17b09ff0ed9', 1, 38, 3, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(114, 'ea15564ca973a2f7949cad1bb7aadcd9', 1, 38, 4, 2, '0.000', '0.000', '1.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(115, 'b06d9af0a58582f1e397e899d743a905', 1, 39, 2, 1, '0.000', '0.000', '5.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(116, '3e6009ebcbae55783b35286b0a8b30ae', 1, 39, 3, 1, '0.000', '0.000', '5.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(117, 'fe4316305aaec8cf0ddf78586ec09eed', 1, 39, 4, 1, '0.000', '0.000', '5.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(118, '4a7532e402a4cd2d5a541645a2d8d6e7', 1, 40, 2, 4, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(119, 'c5aef0cac996712b059f05d25c6ac8fa', 1, 40, 3, 4, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(120, 'aad75714bbf5dc3d841f2610895a4756', 1, 40, 4, 4, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(121, '385e7e3fe01f347ff27e1d117bcd70b3', 1, 41, 2, 5, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(122, 'fc3574db26bf4662586af11ad72415cb', 1, 41, 3, 5, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(123, '4b0919ddfc43abcc4024bbc3f75ffe01', 1, 41, 4, 5, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(124, '5a1c08367de493162529de1b23bd3237', 1, 42, 2, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(125, 'cf7024fd04b0b679e150541d7fa86ccb', 1, 42, 3, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(126, '60180e979c181bd6f4fc7af42cd5f884', 1, 42, 4, 1, '0.000', '0.000', '3.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(127, '6be02f22c6e417cc4e8a4f4620af785c', 1, 43, 2, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(128, '9747eff2a5b4a372dfd7d18784bcda98', 1, 43, 3, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(129, 'a9e445fa82922bef76bc88b0e6817b91', 1, 43, 4, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(130, '439416c3830462ded3c247a9303d5096', 1, 44, 2, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(131, 'c63c2b72d65e5520627059e2ba6a9ee1', 1, 44, 3, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(132, '55a1468fb24f02b61b7748ccf6654ef0', 1, 44, 4, 1, '0.000', '0.000', '3.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(133, '7f516b38327b8aad37541d1751a56986', 1, 45, 2, 1, '0.000', '0.000', '14.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(134, '800c43806561c808c730a55cbaae003e', 1, 45, 3, 1, '0.000', '0.000', '14.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(135, 'ac927c5d54d32c0c5fb6828a1119685a', 1, 45, 4, 1, '0.000', '0.000', '14.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(136, 'f4ffeae1e8b9deade294af17c4e6ec15', 1, 46, 2, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(137, '976532af5cbcd28e5b8ae2aa0ee21a11', 1, 46, 3, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(138, 'a6d541bff387e48c937ad1ed21dc37ec', 1, 46, 4, 1, '0.000', '0.000', '0.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(139, 'aa6befa3cd78cc551393e55e9395803b', 1, 47, 2, 1, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(140, '7d0c5d013532d1353e12d4d9adc6edc0', 1, 47, 3, 1, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(141, '2656b66ea0900c79126feb20fef8ec25', 1, 47, 4, 1, '0.000', '0.000', '0.750', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(142, 'd0a38e2d4c171e86a0425f92a65422a6', 1, 48, 2, 1, '0.000', '0.000', '6.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(143, '5311f84d355ff772865ec7d968d3e8c2', 1, 48, 3, 1, '0.000', '0.000', '6.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(144, '2edd87d6d1082d880942ba8258fbccb2', 1, 48, 4, 1, '0.000', '0.000', '6.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(145, 'a9108e7895f710ac9e148d02b3ce30b9', 1, 49, 2, 1, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(146, '127d280c90d7a7626987640b462c2f7f', 1, 49, 3, 1, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(147, '48644100b363135085802254bd70d5ab', 1, 49, 4, 1, '0.000', '0.000', '4.500', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(148, '1fe7d0584f1a3e92696745b720f510aa', 1, 50, 2, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(149, '7bdd395d9972f743450d265518b23482', 1, 50, 3, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(150, '080effaab517af6e1e25a4925fad09f2', 1, 50, 4, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(151, '6b46d83b588ef833b05021473825edfe', 1, 51, 2, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(152, '194cec084552656c01b1ac65b90eedb0', 1, 51, 3, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(153, 'b355625ccab6ee40c2be908bb15663cf', 1, 51, 4, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(154, 'bc2c306a78d8f66b16a7a49804b545ca', 1, 52, 2, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(155, '586732c11a97cb46671ca122d651809e', 1, 52, 3, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(156, '07233c34a5234f6bacd93b5cb21408a6', 1, 52, 4, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(157, '202f8b8b677b2ce6523b7c3d3537cac7', 1, 53, 2, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(158, '81b62889a135b9e546c9e309ac84a046', 1, 53, 3, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(159, '23b6053c3fba22b653c9aed32581c127', 1, 53, 4, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(160, 'add29c1b3f5b7a60657e06baa582001f', 1, 54, 2, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(161, '643a74a4dacc2f8b24a0f4e4ca820b36', 1, 54, 3, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(162, '353df8c009909c64a7d16318a37f17b0', 1, 54, 4, 2, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(163, 'f60d23352c735cfb0b0f5dd7244a47cb', 1, 55, 2, 1, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(164, 'bdadf042409a8ad8a28b75c7bd257b5f', 1, 55, 3, 1, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(165, '2c8d88f582a47277d15b059c4c849476', 1, 55, 4, 1, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(166, '229b1bd343b0a32edfca6434a2ed1d2b', 1, 56, 2, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(167, '6161e75ea43b23f618b96c8efcadf7a0', 1, 56, 3, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(168, '7ad81b230e3f2616317765c786c6c6de', 1, 56, 4, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(169, '36d62caa4c15e66f40b4878cc5967fe0', 1, 57, 2, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(170, 'a8032f0171a0325b8c477422a0e57a45', 1, 57, 3, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(171, 'e92d4b576bc7afa84fd7f06b684735a7', 1, 57, 4, 5, '0.000', '0.000', '0.000', '0.000', '0.000', 1, '2023-02-09 14:55:19', 4),
(172, 'b776c2d8060609000e87dee7f7defa9f', 1, 59, 2, 2, '1.900', '0.000', '5.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1),
(173, '43767202c4f7b924e87aed61354b1015', 1, 59, 3, 2, '1.900', '0.000', '7.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1),
(174, 'fec9af9a8c92b75feac8abd0b8a1603d', 1, 59, 4, 2, '1.900', '0.000', '9.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1);

--
-- Triggers `product_prices`
--
DROP TRIGGER IF EXISTS `zProductPriceAfUpdate`;
DELIMITER //
CREATE TRIGGER `zProductPriceAfUpdate` AFTER UPDATE ON `product_prices`
 FOR EACH ROW BEGIN
	INSERT INTO product_price_histories (branch_id, price_type_id, product_id, uom_id, unit_cost, old_amount_before, old_amount, old_percent, old_add_on, old_set_type, new_amount_before, new_amount, new_percent, new_add_on, new_set_type, created, created_by) VALUES
	(NEW.branch_id, OLD.price_type_id, OLD.product_id, OLD.uom_id, OLD.old_unit_cost, OLD.amount_before, OLD.amount, OLD.percent, OLD.add_on, OLD.set_type, NEW.amount_before, NEW.amount, NEW.percent, NEW.add_on, NEW.set_type, NEW.created, NEW.created_by);
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zProductPriceBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductPriceBfInsert` BEFORE INSERT ON `product_prices`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.branch_id = "" OR NEW.branch_id = NULL OR NEW.product_id = "" OR NEW.product_id = NULL OR NEW.price_type_id = "" OR NEW.price_type_id = NULL OR NEW.uom_id = "" OR NEW.uom_id = NULL OR NEW.set_type = "" OR NEW.set_type = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_price_histories`
--

CREATE TABLE IF NOT EXISTS `product_price_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `price_type_id` int(11) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `unit_cost` decimal(15,3) DEFAULT NULL,
  `old_amount_before` decimal(15,3) DEFAULT NULL,
  `old_amount` decimal(15,3) DEFAULT NULL,
  `old_percent` decimal(6,3) DEFAULT NULL,
  `old_add_on` decimal(15,3) DEFAULT NULL,
  `old_set_type` tinyint(4) DEFAULT NULL,
  `new_amount_before` decimal(15,3) DEFAULT NULL,
  `new_amount` decimal(15,3) DEFAULT NULL,
  `new_percent` decimal(6,3) DEFAULT NULL,
  `new_add_on` decimal(15,3) DEFAULT NULL,
  `new_set_type` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filters` (`product_id`,`price_type_id`,`uom_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `product_price_histories`
--

INSERT INTO `product_price_histories` (`id`, `branch_id`, `price_type_id`, `product_id`, `uom_id`, `unit_cost`, `old_amount_before`, `old_amount`, `old_percent`, `old_add_on`, `old_set_type`, `new_amount_before`, `new_amount`, `new_percent`, `new_add_on`, `new_set_type`, `created`, `created_by`) VALUES
(1, 1, 2, 59, 2, '1.900', '0.000', '0.000', '0.000', '0.000', 1, '0.000', '5.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1),
(2, 1, 3, 59, 2, '1.900', '0.000', '0.000', '0.000', '0.000', 1, '0.000', '7.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1),
(3, 1, 4, 59, 2, '1.900', '0.000', '0.000', '0.000', '0.000', 1, '0.000', '9.000', '0.000', '0.000', 1, '2023-05-31 16:03:40', 1),
(4, 1, 2, 28, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1),
(5, 1, 3, 28, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1),
(6, 1, 4, 28, 1, '0.000', '0.000', '10.000', '0.000', '0.000', 1, '0.000', '10.000', '0.000', '0.000', 1, '2023-05-31 16:05:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_unit_cost_histories`
--

CREATE TABLE IF NOT EXISTS `product_unit_cost_histories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) NOT NULL DEFAULT '0',
  `product_id` bigint(20) DEFAULT NULL,
  `old_cost` decimal(18,9) DEFAULT NULL,
  `new_cost` decimal(18,9) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id_product_id` (`purchase_order_id`,`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `product_unit_cost_histories`
--

INSERT INTO `product_unit_cost_histories` (`id`, `purchase_order_id`, `product_id`, `old_cost`, `new_cost`, `type`, `created`, `created_by`) VALUES
(1, 2, 1, '0.000000000', '30.000000000', 'PB', '2023-05-30 15:32:29', 1),
(2, 3, 4, '0.000000000', '2.300000000', 'PB', '2023-05-30 15:37:35', 1),
(3, 5, 5, '0.000000000', '10.000000000', 'PB', '2023-05-31 15:53:05', 1),
(4, 5, 10, '0.000000000', '1.000000000', 'PB', '2023-05-31 15:53:05', 1),
(5, 5, 15, '0.000000000', '2.000000000', 'PB', '2023-05-31 15:53:05', 1),
(6, 5, 3, '0.000000000', '0.500000000', 'PB', '2023-05-31 15:53:05', 1),
(7, 6, 59, '2.000000000', '1.900000000', 'PB', '2023-05-31 15:57:42', 1),
(8, 7, 28, '0.000000000', '1.000000000', 'PB', '2023-05-31 16:05:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_with_customers`
--

CREATE TABLE IF NOT EXISTS `product_with_customers` (
  `product_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`product_id`,`customer_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_with_packets`
--

CREATE TABLE IF NOT EXISTS `product_with_packets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `main_product_id` int(11) DEFAULT NULL,
  `packet_product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `main_product_id_packet_product_id` (`main_product_id`,`packet_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `product_with_packets`
--
DROP TRIGGER IF EXISTS `zProductWithPacketBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductWithPacketBfInsert` BEFORE INSERT ON `product_with_packets`
 FOR EACH ROW BEGIN
	IF NEW.main_product_id = "" OR NEW.main_product_id = NULL OR NEW.packet_product_id = "" OR NEW.packet_product_id = NULL OR NEW.qty = "" OR NEW.qty = NULL OR NEW.qty_uom_id = "" OR NEW.qty_uom_id = NULL OR NEW.conversion = "" OR NEW.conversion = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_with_skus`
--

CREATE TABLE IF NOT EXISTS `product_with_skus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) DEFAULT NULL,
  `sku` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uom_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `sku` (`sku`),
  KEY `uom_id` (`uom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `product_with_skus`
--
DROP TRIGGER IF EXISTS `zProductWithSkuBfInsert`;
DELIMITER //
CREATE TRIGGER `zProductWithSkuBfInsert` BEFORE INSERT ON `product_with_skus`
 FOR EACH ROW BEGIN
	IF NEW.product_id = "" OR NEW.product_id = NULL OR NEW.sku = "" OR NEW.sku = NULL OR NEW.uom_id = "" OR NEW.uom_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE IF NOT EXISTS `provinces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51 ;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `sys_code`, `name`, `abbr`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(26, 'f1b57977c0471a4ebf078a28a7b79eaf', 'PHNOM PENH', 'PNH', '2014-12-03 11:27:44', 1, '2014-12-03 11:27:44', NULL, 1),
(27, '2cfaa750990b70acb718e9a7ea28e4be', 'BANTEAY MEANCHEY', 'BMC', '2014-12-03 11:27:58', 1, '2014-12-03 11:27:58', NULL, 1),
(28, '474c5eaacd210a5d5024f9a47b15da2f', 'BATTAMBANG', 'BDB', '2014-12-03 11:28:09', 1, '2014-12-03 11:28:09', NULL, 1),
(29, '047fca4a002d628e934b814282c11d2f', 'KAMPONG CHAM', 'KPC', '2014-12-03 11:28:39', 1, '2014-12-03 11:28:39', NULL, 1),
(30, '3bc73a7b2937898ee2a40d89fd21f939', 'KAMPONG CHHNANG', 'KCH', '2014-12-03 11:28:51', 1, '2014-12-03 11:28:51', NULL, 1),
(31, '06fa61d3f49302ad9cb59405efd4890c', 'KAMPONG SPEU', 'KPS', '2014-12-03 11:29:08', 1, '2014-12-03 11:29:08', NULL, 1),
(32, 'faee449b5e53c3553ea6275e848b1c13', 'KAMPONG THOM', 'KPT', '2014-12-03 11:29:19', 1, '2014-12-03 11:29:19', NULL, 1),
(33, 'bf11ce1a07ee94c363f47b22d9af86af', 'KAMPOT', 'KAP', '2014-12-03 11:29:39', 1, '2014-12-03 11:29:39', NULL, 1),
(34, '8d0dda52f7b41751d1bc407548504c69', 'KANDAL', 'KND', '2014-12-03 11:29:51', 1, '2014-12-03 11:29:51', NULL, 1),
(35, 'ba7fe9da8d7baa44eed4dd9456b894e1', 'KEP', 'KEP', '2014-12-03 11:30:02', 1, '2014-12-03 11:30:02', NULL, 1),
(36, 'f62b6c848a68479c8538f6ae11fb6579', 'KRATIE', 'KRT', '2014-12-03 11:30:12', 1, '2014-12-03 11:30:12', NULL, 1),
(37, 'cd34420e5e22c5f3ed08fc7c5ed76c1d', 'MONDULKIRI', 'MKR', '2014-12-03 11:30:22', 1, '2014-12-03 11:30:22', NULL, 1),
(38, '251e199f12ff121c941df156978b54e7', 'ODDAR MEANCHEY', 'OMC', '2014-12-03 11:30:35', 1, '2014-12-03 11:30:35', NULL, 1),
(39, '87e6d5327a2c2814de4cdca259b89ad8', 'PAILIN', 'PAL', '2014-12-03 11:30:45', 1, '2014-12-03 11:30:45', NULL, 1),
(40, '69b6a0a2da0bd20859fd864f933c6705', 'PREAH SIHANOUK', 'SHV', '2014-12-03 11:30:55', 1, '2014-12-03 11:30:55', NULL, 1),
(41, '73f129ac5009cff04d42e1aa01f525b7', 'PREAH VIHEAR', 'PVH', '2014-12-03 11:31:10', 1, '2014-12-03 11:31:10', NULL, 1),
(42, '42a446e138f4529811e55c4c84571c86', 'PURSAT', 'PUR', '2014-12-03 11:31:23', 1, '2014-12-03 11:31:23', NULL, 1),
(43, 'a9f666fe8e2e715c7df754ca6447307e', 'PREY VENG', 'PRV', '2014-12-03 11:31:44', 1, '2014-12-03 11:31:44', NULL, 1),
(44, '081e97d0ddca6298ae77a2075028e12a', 'RATANAKIRI', 'RKR', '2014-12-03 11:31:58', 1, '2014-12-03 11:31:58', NULL, 1),
(45, '51ad012d8a57bed387a488fcd8fb593d', 'SIEM REAP', 'SMR', '2014-12-03 11:32:11', 1, '2014-12-03 11:32:11', NULL, 1),
(46, 'a6e784004da8c809d3396e8b1ee26885', 'STUNG TRENG', 'STG', '2014-12-03 11:32:19', 1, '2014-12-03 11:32:19', NULL, 1),
(47, 'b68265acacd680610e6191c36821a1aa', 'SVAY RIENG', 'SVR', '2014-12-03 11:32:30', 1, '2014-12-03 11:32:30', NULL, 1),
(48, 'f32b1fea1b7b21939c070ff181ee6a00', 'TAKEO', 'TKE', '2014-12-03 11:32:40', 1, '2014-12-03 11:32:40', NULL, 1),
(49, '760148e8d94332593ac6d0cde9ad6a1c', 'TBONG KHMUM', 'TBK', '2014-12-03 11:32:51', 1, '2014-12-03 11:32:51', NULL, 1),
(50, 'a3a4ef3d55b3071c80ccf1f3b62c197c', 'KOH KONG', 'KOK', '2014-12-03 11:32:59', 1, '2014-12-03 11:32:59', NULL, 1);

--
-- Triggers `provinces`
--
DROP TRIGGER IF EXISTS `zProvinceBfInsert`;
DELIMITER //
CREATE TRIGGER `zProvinceBfInsert` BEFORE INSERT ON `provinces`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.abbr = "" OR NEW.abbr = NULL THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_request_id` int(11) DEFAULT NULL,
  `vendor_consignment_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `ap_id` int(11) DEFAULT NULL,
  `po_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `total_amount` decimal(15,9) DEFAULT NULL,
  `total_deposit` decimal(15,9) DEFAULT NULL,
  `discount_amount` decimal(15,9) DEFAULT NULL,
  `discount_percent` decimal(7,3) DEFAULT NULL,
  `balance` decimal(15,9) DEFAULT NULL,
  `vat_chart_account_id` int(11) DEFAULT NULL,
  `total_vat` decimal(15,9) DEFAULT NULL,
  `vat_percent` decimal(7,3) DEFAULT NULL,
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_term_id` int(11) DEFAULT NULL,
  `shipment_id` int(11) DEFAULT NULL,
  `principal` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_deposit_reference` tinyint(4) DEFAULT '0',
  `is_update_cost` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_filter_second` (`purchase_request_id`,`ap_id`),
  KEY `key_search` (`po_code`,`invoice_code`,`invoice_date`,`balance`,`order_date`,`created_by`,`modified_by`,`status`),
  KEY `key_filters` (`company_id`,`branch_id`,`location_group_id`,`location_id`,`vendor_id`,`currency_center_id`,`vat_chart_account_id`,`vat_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE IF NOT EXISTS `purchase_order_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_id` int(10) unsigned DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,9) DEFAULT '0.000000000',
  `discount_percent` decimal(7,3) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `max_order` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `default_cost` decimal(15,9) NOT NULL DEFAULT '0.000000000',
  `new_unit_cost` decimal(15,9) NOT NULL DEFAULT '0.000000000',
  `unit_cost` decimal(15,9) DEFAULT '0.000000000',
  `total_cost` decimal(15,9) DEFAULT '0.000000000',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_expired` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `discount_id` (`discount_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_miscs`
--

CREATE TABLE IF NOT EXISTS `purchase_order_miscs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(10) unsigned DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,9) DEFAULT '0.000000000',
  `discount_percent` decimal(7,3) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(10) DEFAULT NULL,
  `unit_cost` decimal(15,9) DEFAULT '0.000000000',
  `total_cost` decimal(15,9) DEFAULT '0.000000000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_services`
--

CREATE TABLE IF NOT EXISTS `purchase_order_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(10) unsigned DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `discount_amount` decimal(15,9) DEFAULT '0.000000000',
  `discount_percent` decimal(7,3) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `unit_cost` decimal(15,9) DEFAULT '0.000000000',
  `total_cost` decimal(15,9) DEFAULT '0.000000000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `service_id` (`service_id`),
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receives`
--

CREATE TABLE IF NOT EXISTS `purchase_receives` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_receive_result_id` int(10) unsigned DEFAULT NULL,
  `purchase_order_id` int(10) unsigned DEFAULT NULL,
  `purchase_order_detail_id` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_expired` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_filter` (`purchase_receive_result_id`,`purchase_order_id`,`purchase_order_detail_id`(255),`status`),
  KEY `key_search` (`product_id`,`qty_uom_id`,`received_date`,`created_by`,`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receive_results`
--

CREATE TABLE IF NOT EXISTS `purchase_receive_results` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_id` int(11) unsigned DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE IF NOT EXISTS `purchase_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `pr_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `currency_center_id` int(11) NOT NULL,
  `ref_quotation` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipment_id` int(11) DEFAULT NULL,
  `port_of_dischange_id` int(11) DEFAULT NULL,
  `final_place_of_delivery_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,9) NOT NULL DEFAULT '0.000000000',
  `total_deposit` decimal(15,9) DEFAULT '0.000000000',
  `total_vat` decimal(15,9) NOT NULL DEFAULT '0.000000000',
  `vat_percent` decimal(7,3) NOT NULL,
  `vat_setting_id` int(11) NOT NULL,
  `vat_calculate` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_close` tinyint(4) DEFAULT '0',
  `status` tinyint(50) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_filter_second` (`shipment_id`,`port_of_dischange_id`,`final_place_of_delivery_id`),
  KEY `key_search` (`pr_code`,`order_date`,`created_by`,`modified_by`,`is_close`,`status`),
  KEY `filter_first` (`company_id`,`branch_id`,`vendor_id`,`currency_center_id`,`vat_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `purchase_requests`
--
DROP TRIGGER IF EXISTS `zPurchaseRequestBeforeInsert`;
DELIMITER //
CREATE TRIGGER `zPurchaseRequestBeforeInsert` BEFORE INSERT ON `purchase_requests`
 FOR EACH ROW BEGIN
	IF NEW.company_id IS NULL OR NEW.branch_id IS NULL OR NEW.vendor_id IS NULL OR NEW.pr_code IS NULL OR NEW.pr_code = '' OR NEW.order_date IS NULL OR NEW.order_date = '' OR NEW.order_date = '0000-00-00' OR NEW.currency_center_id IS NULL OR NEW.vat_percent IS NULL OR NEW.vat_setting_id IS NULL OR NEW.vat_calculate IS NULL OR NEW.total_vat IS NULL OR NEW.total_amount IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_details`
--

CREATE TABLE IF NOT EXISTS `purchase_request_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_request_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `unit_cost` decimal(15,9) DEFAULT '0.000000000',
  `total_cost` decimal(15,9) DEFAULT '0.000000000',
  `note` text COLLATE utf8_unicode_ci,
  `is_close` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_request_id` (`purchase_request_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_services`
--

CREATE TABLE IF NOT EXISTS `purchase_request_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_request_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `unit_cost` decimal(15,9) DEFAULT '0.000000000',
  `total_cost` decimal(15,9) DEFAULT '0.000000000',
  `note` text COLLATE utf8_unicode_ci,
  `is_close` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_request_id` (`purchase_request_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_term_conditions`
--

CREATE TABLE IF NOT EXISTS `purchase_request_term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_request_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_search` (`purchase_request_id`,`term_condition_type_id`,`term_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE IF NOT EXISTS `purchase_returns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `ap_id` int(11) DEFAULT NULL,
  `pr_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_amount` decimal(15,9) DEFAULT NULL,
  `total_amount_po` decimal(15,9) DEFAULT NULL,
  `balance` decimal(15,9) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `vat_chart_account_id` int(11) DEFAULT NULL,
  `total_vat` decimal(15,9) DEFAULT NULL,
  `vat_percent` decimal(7,3) DEFAULT NULL,
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_search` (`pr_code`,`balance`,`order_date`,`created_by`,`modified_by`,`status`),
  KEY `key_filter` (`company_id`,`location_group_id`,`location_id`,`vendor_id`,`currency_center_id`,`ap_id`,`vat_chart_account_id`,`vat_setting_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_details`
--

CREATE TABLE IF NOT EXISTS `purchase_return_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_return_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `unit_price` decimal(15,9) DEFAULT '0.000000000',
  `total_price` decimal(15,9) DEFAULT '0.000000000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_miscs`
--

CREATE TABLE IF NOT EXISTS `purchase_return_miscs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(10) DEFAULT NULL,
  `unit_price` decimal(15,9) DEFAULT '0.000000000',
  `total_price` decimal(15,9) DEFAULT '0.000000000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_receipts`
--

CREATE TABLE IF NOT EXISTS `purchase_return_receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_return_id` int(10) unsigned DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `receipt_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_us` decimal(15,9) DEFAULT '0.000000000',
  `amount_other` decimal(15,9) DEFAULT '0.000000000',
  `total_amount` decimal(15,9) DEFAULT '0.000000000',
  `balance` decimal(15,9) DEFAULT '0.000000000',
  `balance_other` decimal(15,9) DEFAULT '0.000000000',
  `change` decimal(15,9) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_void` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`),
  KEY `receipt_code` (`receipt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_receives`
--

CREATE TABLE IF NOT EXISTS `purchase_return_receives` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int(10) unsigned DEFAULT NULL,
  `purchase_return_detail_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`),
  KEY `purchase_return_detail_id` (`purchase_return_detail_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_services`
--

CREATE TABLE IF NOT EXISTS `purchase_return_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int(10) unsigned DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `unit_price` decimal(15,9) DEFAULT '0.000000000',
  `total_price` decimal(15,9) DEFAULT '0.000000000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pvs`
--

CREATE TABLE IF NOT EXISTS `pvs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchase_order_id` int(10) unsigned DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `pv_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_us` decimal(15,9) DEFAULT '0.000000000',
  `amount_other` decimal(15,9) DEFAULT '0.000000000',
  `total_amount` decimal(15,9) DEFAULT '0.000000000',
  `discount` decimal(15,9) DEFAULT '0.000000000',
  `discount_other` decimal(15,9) DEFAULT '0.000000000',
  `balance` decimal(15,9) DEFAULT '0.000000000',
  `balance_other` decimal(15,9) DEFAULT '0.000000000',
  `pay_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_void` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `pv_code` (`pv_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pvs`
--

INSERT INTO `pvs` (`id`, `sys_code`, `purchase_order_id`, `branch_id`, `exchange_rate_id`, `currency_center_id`, `chart_account_id`, `pv_code`, `amount_us`, `amount_other`, `total_amount`, `discount`, `discount_other`, `balance`, `balance_other`, `pay_date`, `due_date`, `created`, `created_by`, `modified`, `modified_by`, `is_void`) VALUES
(1, '95cbd964186adcc1a7ce3a48bc4d8888', 3, 1, 0, NULL, 2, '23PRC0000001', '57.500000000', '0.000000000', '57.500000000', '0.000000000', '0.000000000', '0.000000000', '0.000000000', '2023-05-30', NULL, '2023-05-30 15:38:56', 1, '2023-05-30 15:38:56', NULL, 0),
(2, '336307ba75e7728c69726db62ef2192c', 5, 1, 0, NULL, 2, '23PRC0000002', '4350.000000000', '0.000000000', '4350.000000000', '0.000000000', '0.000000000', '0.000000000', '0.000000000', '2023-05-31', NULL, '2023-05-31 15:54:01', 1, '2023-05-31 15:54:01', NULL, 0),
(3, '1a192abac5bb976ec86c67bf24b0aa37', 6, 1, 0, NULL, 2, '23PRC0000003', '200.000000000', '0.000000000', '200.000000000', '0.000000000', '0.000000000', '0.000000000', '0.000000000', '2023-05-31', NULL, '2023-05-31 15:57:58', 1, '2023-05-31 15:57:58', NULL, 0),
(4, 'b4c6ff7318ec5bf9901ff95f6d191500', 8, 1, 0, NULL, 2, '23PRC0000004', '500.000000000', '0.000000000', '500.000000000', '0.000000000', '0.000000000', '0.000000000', '0.000000000', '2023-05-31', NULL, '2023-05-31 16:06:32', 1, '2023-05-31 16:06:32', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `queued_doctors`
--

CREATE TABLE IF NOT EXISTS `queued_doctors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0 is cancel; 1 is queue waiting, 2 have been done with consultation, 3 pay already',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`queue_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `queued_doctors`
--

INSERT INTO `queued_doctors` (`id`, `queue_id`, `doctor_id`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 1, 6, '2023-06-07 12:19:18', 1, '2023-06-07 12:19:18', NULL, 1),
(2, 2, 2, '2023-06-07 14:04:01', 1, '2023-06-07 14:04:01', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `queued_doctor_waitings`
--

CREATE TABLE IF NOT EXISTS `queued_doctor_waitings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `number_taken` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`queue_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `queued_doctor_waitings`
--

INSERT INTO `queued_doctor_waitings` (`id`, `queue_id`, `doctor_id`, `room_id`, `number_taken`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, 1, 6, NULL, 1, '2023-06-07 12:19:18', 1, '2023-06-07 12:19:18', NULL),
(2, 2, 2, NULL, 1, '2023-06-07 14:04:01', 1, '2023-06-07 14:04:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `queued_labos`
--

CREATE TABLE IF NOT EXISTS `queued_labos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`queue_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `queued_mid_wifes`
--

CREATE TABLE IF NOT EXISTS `queued_mid_wifes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1 is queue waiting, 2 have been done with consultation, 3 pay already',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`queue_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE IF NOT EXISTS `queues` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) DEFAULT NULL,
  `patient_type_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1 is active, 2 have action, 3 ready payment; 4 is void',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `patient_id`, `patient_type_id`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 1, 2, '2023-06-07 12:19:18', 1, '2023-06-07 12:19:18', NULL, 1),
(2, 2, 2, '2023-06-07 14:04:01', 1, '2023-06-07 14:04:01', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `queue_numbers`
--

CREATE TABLE IF NOT EXISTS `queue_numbers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `number_taken` int(11) DEFAULT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE IF NOT EXISTS `quotations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_contact_id` int(11) DEFAULT NULL,
  `quotation_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quotation_date` date DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `total_vat` decimal(15,3) DEFAULT NULL,
  `vat_percent` decimal(5,3) DEFAULT NULL,
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` tinyint(4) DEFAULT NULL COMMENT '1: Before Discount, Mark Up; 2: After Discount, Mark Up',
  `discount` decimal(15,3) DEFAULT NULL,
  `discount_percent` decimal(6,3) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `total_deposit` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `share_save_option` tinyint(4) DEFAULT NULL,
  `user_share_id` int(11) DEFAULT NULL,
  `share_option` tinyint(4) DEFAULT NULL COMMENT '1: Only me; 2: Everyone; 3: User customize; 4: Everyone but except user',
  `share_user` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `share_except_user` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_close` tinyint(4) DEFAULT '0',
  `is_approve` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_search` (`quotation_code`,`quotation_date`,`created_by`,`edited_by`,`modified_by`,`approved_by`,`is_close`,`is_approve`,`status`),
  KEY `key_search_share` (`user_share_id`,`share_option`,`share_user`(255),`share_except_user`(255)),
  KEY `key_filter` (`company_id`,`customer_id`,`customer_contact_id`,`currency_center_id`,`price_type_id`,`vat_setting_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_details`
--

CREATE TABLE IF NOT EXISTS `quotation_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_cost` decimal(15,3) DEFAULT '0.000',
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`id`),
  KEY `quotaion_id` (`quotation_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_miscs`
--

CREATE TABLE IF NOT EXISTS `quotation_miscs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`id`),
  KEY `quotaion_id` (`quotation_id`),
  KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_services`
--

CREATE TABLE IF NOT EXISTS `quotation_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  PRIMARY KEY (`id`),
  KEY `quotaion_id` (`quotation_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_term_conditions`
--

CREATE TABLE IF NOT EXISTS `quotation_term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_id_term_condition_type_id_term_condition_id` (`quotation_id`,`term_condition_type_id`,`term_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reasons`
--

CREATE TABLE IF NOT EXISTS `reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `reasons`
--

INSERT INTO `reasons` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '48de4e2976edaf09048dc7b9d48652c5', 'Customer Return', '2017-09-14 17:36:15', 1, '2019-05-03 16:13:02', 1, 1),
(2, 'd9b83956356d7df7b1365be0b8e992ca', 'Break', '2019-05-03 16:12:58', 1, '2019-05-03 16:12:58', NULL, 1);

--
-- Triggers `reasons`
--
DROP TRIGGER IF EXISTS `zReasonBfInsert`;
DELIMITER //
CREATE TRIGGER `zReasonBfInsert` BEFORE INSERT ON `reasons`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE IF NOT EXISTS `receipts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) DEFAULT NULL,
  `chart_account_id` bigint(20) DEFAULT NULL,
  `receipt_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exchange_rate_id` bigint(20) DEFAULT NULL,
  `total_amount_paid` double DEFAULT '0',
  `balance` double DEFAULT '0',
  `total_dis` double DEFAULT '0',
  `total_dis_p` double DEFAULT '0',
  `pay_date` date DEFAULT NULL,
  `due_date` float DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_void` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `receipt_code` (`receipt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `receive_payments`
--

CREATE TABLE IF NOT EXISTS `receive_payments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `cgroup_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `deposit_to` int(11) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filters` (`company_id`,`branch_id`,`cgroup_id`,`customer_id`),
  KEY `searchs` (`date`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `receive_payment_details`
--

CREATE TABLE IF NOT EXISTS `receive_payment_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receive_payment_id` bigint(20) DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `amount_due` decimal(15,3) DEFAULT '0.000',
  `paid` decimal(15,3) DEFAULT '0.000',
  `paid_other` decimal(15,3) DEFAULT '0.000',
  `discount` decimal(15,3) DEFAULT '0.000',
  `discount_other` decimal(15,3) DEFAULT '0.000',
  `balance` decimal(15,3) DEFAULT '0.000',
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reconciles`
--

CREATE TABLE IF NOT EXISTS `reconciles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `service_charge_gl_id` bigint(20) DEFAULT NULL,
  `interested_earned_gl_id` bigint(20) DEFAULT NULL,
  `diff_gl_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filters` (`chart_account_id`,`service_charge_gl_id`,`interested_earned_gl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE IF NOT EXISTS `referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sex` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT '0',
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `description` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1:active 2:edit 3:delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `name`, `sex`, `telephone`, `address`, `email`, `dob`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Sok Syla', 'M', '012457896', NULL, NULL, '2004-04-16', NULL, '2023-06-07 12:10:42', 1, '2023-06-07 12:10:42', NULL, 1),
(2, 'Prom Seyha', 'M', '0156321478', NULL, NULL, '2005-06-06', NULL, '2023-06-07 12:11:08', 1, '2023-06-07 12:11:08', NULL, 1),
(3, 'Vong Malina', 'F', '077777777', NULL, NULL, '2000-06-06', NULL, '2023-06-07 12:12:02', 1, '2023-06-07 12:12:02', NULL, 1),
(4, 'Kong Chanreaksmey', 'M', '0181236548', NULL, NULL, '2005-03-25', NULL, '2023-06-07 12:13:07', 1, '2023-06-07 12:13:07', NULL, 1),
(5, 'Chin Kakada', 'M', '012141563', NULL, NULL, '2005-02-11', NULL, '2023-06-07 12:13:36', 1, '2023-06-07 12:13:36', NULL, 1),
(6, 'Chin Socheat', 'M', '015232323', NULL, NULL, '2005-03-11', NULL, '2023-06-07 12:14:26', 1, '2023-06-07 12:14:26', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `report_sales_by_days`
--

CREATE TABLE IF NOT EXISTS `report_sales_by_days` (
  `date` date NOT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sales_rep_id` int(11) NOT NULL DEFAULT '0',
  `s_total_amount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `s_total_discount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `s_total_vat` decimal(15,3) NOT NULL DEFAULT '0.000',
  `p_total_amount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `p_total_discount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `p_total_vat` decimal(15,3) NOT NULL DEFAULT '0.000',
  `c_total_amount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `c_total_discount` decimal(15,3) NOT NULL DEFAULT '0.000',
  `c_total_mark_up` decimal(15,3) NOT NULL DEFAULT '0.000',
  `c_total_vat` decimal(15,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`date`,`company_id`,`branch_id`,`customer_id`,`sales_rep_id`),
  KEY `filters` (`date`,`company_id`,`branch_id`,`customer_id`,`sales_rep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_sales_by_months`
--

CREATE TABLE IF NOT EXISTS `report_sales_by_months` (
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sales_rep_id` int(11) NOT NULL DEFAULT '0',
  `s_total_amount` decimal(15,3) NOT NULL,
  `s_total_discount` decimal(15,3) NOT NULL,
  `s_total_vat` decimal(15,3) NOT NULL,
  `p_total_amount` decimal(15,3) NOT NULL,
  `p_total_discount` decimal(15,3) NOT NULL,
  `p_total_vat` decimal(15,3) NOT NULL,
  `c_total_amount` decimal(15,3) NOT NULL,
  `c_total_discount` decimal(15,3) NOT NULL,
  `c_total_mark_up` decimal(15,3) NOT NULL,
  `c_total_vat` decimal(15,3) NOT NULL,
  PRIMARY KEY (`month`,`year`,`company_id`,`branch_id`,`customer_id`,`sales_rep_id`),
  KEY `filters` (`month`,`year`,`company_id`,`branch_id`,`customer_id`,`sales_rep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_stocks`
--

CREATE TABLE IF NOT EXISTS `request_stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `from_location_group_id` int(11) DEFAULT NULL,
  `to_location_group_id` int(11) DEFAULT NULL,
  `transfer_order_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `index_keys` (`from_location_group_id`,`to_location_group_id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `request_stock_details`
--

CREATE TABLE IF NOT EXISTS `request_stock_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_stock_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_stock_id` (`request_stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_floor_id` int(11) DEFAULT NULL,
  `room_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `screen_display` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: not display; 1: display',
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `company_id`, `room_type_id`, `room_floor_id`, `room_name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `screen_display`, `is_active`) VALUES
(1, 1, 3, 2, 'Room 1', 'VIP Room 1', '2019-03-30 09:41:01', 1, '2019-03-30 09:41:01', 0, 0, 1),
(2, 1, 3, 2, 'Room 2', 'VIP Room 2', '2019-03-30 09:41:45', 1, '2019-03-30 09:41:45', 0, 0, 1),
(3, 1, 3, 2, 'Room 3', 'VIP Room 3', '2019-03-30 09:42:28', 1, '2019-03-30 09:42:28', 0, 0, 1),
(4, 1, 3, 3, 'Room 4', 'VIP Room 4', '2019-03-30 09:42:56', 1, '2019-03-30 09:42:56', 0, 0, 1),
(5, 1, 3, 3, 'Room 5', 'VIP Room 5', '2019-03-30 09:43:24', 1, '2019-03-30 09:43:24', 0, 0, 1),
(6, 1, 3, 3, 'Room 6', 'VIP Room 6', '2019-03-30 09:43:50', 1, '2019-03-30 09:43:50', 0, 0, 1),
(7, 1, 3, 1, '007', 'Best', '2019-05-03 16:18:02', 1, '2019-05-03 16:18:02', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_floors`
--

CREATE TABLE IF NOT EXISTS `room_floors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `room_floors`
--

INSERT INTO `room_floors` (`id`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Ground Floor', NULL, '2016-06-18 10:03:11', 1, NULL, NULL, 1),
(2, 'First Floor', '1', '2016-06-18 10:03:22', 1, '2017-12-28 17:35:35', 1, 1),
(3, 'Second Floor', '2', '2017-12-11 15:39:03', 1, '2017-12-11 15:39:03', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE IF NOT EXISTS `room_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `chart_account_id` int(11) DEFAULT NULL,
  `chart_account_id_expense` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` double DEFAULT NULL,
  `extra_price` double DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `chart_account_id`, `chart_account_id_expense`, `name`, `unit_price`, `extra_price`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, NULL, NULL, 'Emergency Room', NULL, NULL, '', '2015-12-30 15:09:55', 1, '2015-12-30 15:09:55', NULL, 1),
(2, NULL, NULL, 'REA Room', NULL, NULL, 'SICU, Post-Op, Cardiac ICU, Critical ICU', '2015-12-30 15:10:28', 1, '2015-12-30 15:10:28', NULL, 1),
(3, NULL, NULL, 'VIP Room', NULL, NULL, '', '2015-12-30 15:10:41', 1, '2015-12-30 15:10:41', NULL, 1),
(4, NULL, NULL, 'MAT Room', NULL, NULL, '', '2015-12-30 15:10:54', 1, '2015-12-30 15:10:54', NULL, 1),
(5, NULL, NULL, 'Nursery Room', NULL, NULL, 'Room 309', '2015-12-30 15:11:10', 1, '2015-12-30 15:11:10', NULL, 1),
(6, NULL, NULL, 'MICU Room', NULL, NULL, 'Medical ICU', '2015-12-30 15:11:23', 1, '2015-12-30 15:11:37', 1, 1),
(7, NULL, NULL, 'IPD Room', NULL, NULL, '', '2015-12-30 15:11:48', 1, '2015-12-30 15:11:48', NULL, 1),
(8, NULL, NULL, 'OPD Room', NULL, NULL, '', '2016-08-29 15:23:43', 1, '2016-08-29 15:23:43', NULL, 1),
(9, NULL, NULL, 'Imagery', NULL, NULL, '', '2016-11-18 15:53:59', 1, '2016-11-18 15:53:59', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE IF NOT EXISTS `sales_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `delivery_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `queue_id` int(11) DEFAULT NULL,
  `queue_doctor_id` int(11) DEFAULT NULL,
  `customer_contact_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `ar_id` int(11) DEFAULT NULL,
  `payment_term_id` int(11) DEFAULT NULL,
  `price_type_id` int(11) DEFAULT NULL,
  `sales_rep_id` int(11) DEFAULT NULL,
  `deliver_id` int(11) DEFAULT NULL,
  `collector_id` int(11) DEFAULT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `consignment_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `quotation_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_po_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `project` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `so_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `total_amount_kh` decimal(15,3) DEFAULT NULL,
  `total_amount_return` decimal(15,3) DEFAULT NULL,
  `total_deposit` decimal(15,3) DEFAULT NULL,
  `balance` decimal(15,3) DEFAULT NULL,
  `discount` decimal(15,3) DEFAULT NULL,
  `discount_percent` decimal(6,3) DEFAULT NULL,
  `vat_chart_account_id` int(11) DEFAULT NULL,
  `total_vat` decimal(15,3) DEFAULT NULL,
  `vat_percent` decimal(5,3) DEFAULT NULL COMMENT 'Percent (%)',
  `vat_setting_id` int(11) DEFAULT NULL,
  `vat_calculate` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `memo` text COLLATE utf8_unicode_ci,
  `shift_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1:Edit, 1:Issue, 2:Fullfied; 3: Partial',
  `is_deposit_reference` tinyint(4) DEFAULT '0',
  `is_approve` tinyint(4) DEFAULT '1',
  `is_print` tinyint(4) DEFAULT '0',
  `is_reprint` tinyint(4) DEFAULT '0',
  `is_pos` tinyint(4) DEFAULT '0' COMMENT '0: Invoice; 1: POS; 2: Consignment',
  PRIMARY KEY (`id`),
  KEY `key_filter` (`company_id`,`location_group_id`,`location_id`,`customer_id`,`customer_contact_id`,`currency_center_id`,`payment_term_id`,`price_type_id`,`sales_rep_id`,`branch_id`),
  KEY `key_filter_second` (`delivery_id`,`ar_id`,`deliver_id`,`collector_id`,`quotation_id`,`order_id`,`vat_chart_account_id`,`vat_setting_id`,`consignment_id`),
  KEY `key_search` (`quotation_number`,`order_number`,`so_code`,`balance`,`order_date`,`created_by`,`modified_by`,`status`,`is_approve`,`consignment_code`),
  KEY `shift` (`shift_id`),
  KEY `patient_id` (`patient_id`),
  KEY `queue_id` (`queue_id`),
  KEY `queue_doctor_id` (`queue_doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `sales_orders`
--
DROP TRIGGER IF EXISTS `zSalesOrderAfInsert`;
DELIMITER //
CREATE TRIGGER `zSalesOrderAfInsert` AFTER INSERT ON `sales_orders`
 FOR EACH ROW BEGIN
	DECLARE salesMonth int(11);
	DECLARE salesYear int(11);
	SET salesMonth = MONTH(NEW.order_date);
	SET salesYear  = YEAR(NEW.order_date);
	IF NEW.is_pos = 0 AND NEW.status > 0 THEN
		INSERT INTO report_sales_by_days (`date`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `s_total_amount`, `s_total_discount`, `s_total_vat`) 
		VALUES (NEW.order_date, NEW.company_id, NEW.branch_id, NEW.customer_id, NEW.sales_rep_id, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE s_total_amount = s_total_amount + NEW.total_amount, s_total_discount = s_total_discount + NEW.discount, s_total_vat = s_total_vat + NEW.total_vat;
		INSERT INTO report_sales_by_months (`month`, `year`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `s_total_amount`, `s_total_discount`, `s_total_vat`) 
		VALUES (salesMonth, salesYear, NEW.company_id, NEW.branch_id, NEW.customer_id, NEW.sales_rep_id, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE s_total_amount = s_total_amount + NEW.total_amount, s_total_discount = s_total_discount + NEW.discount, s_total_vat = s_total_vat + NEW.total_vat;
	ELSEIF NEW.is_pos = 1 AND NEW.status = 2 THEN
		INSERT INTO report_sales_by_days (`date`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `p_total_amount`, `p_total_discount`, `p_total_vat`) 
		VALUES (NEW.order_date, NEW.company_id, NEW.branch_id, NEW.customer_id, 0, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE p_total_amount = p_total_amount + NEW.total_amount, p_total_discount = p_total_discount + NEW.discount, p_total_vat = p_total_vat + NEW.total_vat;
		INSERT INTO report_sales_by_months (`month`, `year`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `p_total_amount`, `p_total_discount`, `p_total_vat`) 
		VALUES (salesMonth, salesYear, NEW.company_id, NEW.branch_id, NEW.customer_id, 0, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE p_total_amount = p_total_amount + NEW.total_amount, p_total_discount = p_total_discount + NEW.discount, p_total_vat = p_total_vat + NEW.total_vat;
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zSalesOrderAfUpdate`;
DELIMITER //
CREATE TRIGGER `zSalesOrderAfUpdate` AFTER UPDATE ON `sales_orders`
 FOR EACH ROW BEGIN
	DECLARE salesMonth int(11);
	DECLARE salesYear int(11);
	SET salesMonth = MONTH(OLD.order_date);
	SET salesYear  = YEAR(OLD.order_date);
	IF (OLD.status > 0 AND NEW.status = -1 AND NEW.is_pos = 0) OR (OLD.status > 0 AND NEW.status = 0 AND NEW.is_pos = 0) THEN
		UPDATE report_sales_by_days SET s_total_amount = s_total_amount - OLD.total_amount, s_total_discount = s_total_discount - OLD.discount, s_total_vat = s_total_vat - OLD.total_vat WHERE date = OLD.order_date AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = OLD.sales_rep_id;
		UPDATE report_sales_by_months SET s_total_amount = s_total_amount - OLD.total_amount, s_total_discount = s_total_discount - OLD.discount, s_total_vat = s_total_vat - OLD.total_vat WHERE month = salesMonth AND year = salesYear AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = OLD.sales_rep_id;
	ELSEIF OLD.status = 2 AND NEW.status = 0 AND NEW.is_pos = 1 THEN
		UPDATE report_sales_by_days SET p_total_amount = p_total_amount - OLD.total_amount, p_total_discount = p_total_discount - OLD.discount, p_total_vat = p_total_vat - OLD.total_vat WHERE date = OLD.order_date AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = 0;
		UPDATE report_sales_by_months SET p_total_amount = p_total_amount - OLD.total_amount, p_total_discount = p_total_discount - OLD.discount, p_total_vat = p_total_vat - OLD.total_vat WHERE month = salesMonth AND year = salesYear AND company_id = OLD.company_id AND branch_id = OLD.branch_id AND customer_id = OLD.customer_id AND sales_rep_id = 0;
   ELSEIF OLD.status = -2 AND NEW.status > 0 AND NEW.is_pos = 0 THEN
		INSERT INTO report_sales_by_days (`date`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `s_total_amount`, `s_total_discount`, `s_total_vat`) 
		VALUES (NEW.order_date, NEW.company_id, NEW.branch_id, NEW.customer_id, NEW.sales_rep_id, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE s_total_amount = s_total_amount + NEW.total_amount, s_total_discount = s_total_discount + NEW.discount, s_total_vat = s_total_vat + NEW.total_vat;
		INSERT INTO report_sales_by_months (`month`, `year`, `company_id`, `branch_id`, `customer_id`, `sales_rep_id`, `s_total_amount`, `s_total_discount`, `s_total_vat`) 
		VALUES (salesMonth, salesYear, NEW.company_id, NEW.branch_id, NEW.customer_id, NEW.sales_rep_id, NEW.total_amount, NEW.discount, NEW.total_vat) 
		ON DUPLICATE KEY UPDATE s_total_amount = s_total_amount + NEW.total_amount, s_total_discount = s_total_discount + NEW.discount, s_total_vat = s_total_vat + NEW.total_vat;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_details`
--

CREATE TABLE IF NOT EXISTS `sales_order_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_cost` decimal(15,3) DEFAULT '0.000',
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty_remianing` int(11) DEFAULT NULL COMMENT 'Total Qty Not Delivery ( Small Qty)',
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_miscs`
--

CREATE TABLE IF NOT EXISTS `sales_order_miscs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint(20) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty` double DEFAULT '0',
  `qty_free` double DEFAULT '0',
  `qty_uom_id` int(10) DEFAULT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_receipts`
--

CREATE TABLE IF NOT EXISTS `sales_order_receipts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `receipt_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_us` decimal(15,3) DEFAULT '0.000',
  `amount_other` decimal(15,3) DEFAULT '0.000',
  `discount_us` decimal(15,3) DEFAULT '0.000',
  `discount_other` decimal(15,3) DEFAULT '0.000',
  `total_amount` decimal(15,3) DEFAULT '0.000',
  `total_amount_other` decimal(15,3) DEFAULT '0.000',
  `balance` decimal(15,3) DEFAULT '0.000',
  `balance_other` decimal(15,3) DEFAULT '0.000',
  `change` decimal(15,3) DEFAULT NULL,
  `change_other` decimal(15,3) DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_void` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `receipt_code` (`receipt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_services`
--

CREATE TABLE IF NOT EXISTS `sales_order_services` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(10) DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `discount_amount` decimal(15,3) DEFAULT '0.000',
  `discount_percent` decimal(5,3) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_free` int(11) DEFAULT '0',
  `unit_price` decimal(15,3) DEFAULT '0.000',
  `total_price` decimal(15,3) DEFAULT '0.000',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_term_conditions`
--

CREATE TABLE IF NOT EXISTS `sales_order_term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_id_term_condition_type_id_term_condition_id` (`sales_order_id`,`term_condition_type_id`,`term_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_targets`
--

CREATE TABLE IF NOT EXISTS `sales_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `m1` decimal(15,3) DEFAULT NULL,
  `m2` decimal(15,3) DEFAULT NULL,
  `m3` decimal(15,3) DEFAULT NULL,
  `m4` decimal(15,3) DEFAULT NULL,
  `m5` decimal(15,3) DEFAULT NULL,
  `m6` decimal(15,3) DEFAULT NULL,
  `m7` decimal(15,3) DEFAULT NULL,
  `m8` decimal(15,3) DEFAULT NULL,
  `m9` decimal(15,3) DEFAULT NULL,
  `m10` decimal(15,3) DEFAULT NULL,
  `m11` decimal(15,3) DEFAULT NULL,
  `m12` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `is_approve` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_search` (`company_id`,`employee_id`,`year`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `sys_code`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '9c1121eeb2186b79de3af939d33e5ffe', 'Consult', '', '2023-02-21 13:54:33', 1, '2023-02-21 13:54:33', NULL, 1);

--
-- Triggers `sections`
--
DROP TRIGGER IF EXISTS `zSectionBfInsert`;
DELIMITER //
CREATE TRIGGER `zSectionBfInsert` BEFORE INSERT ON `sections`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `section_companies`
--

CREATE TABLE IF NOT EXISTS `section_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `section_companies`
--

INSERT INTO `section_companies` (`id`, `section_id`, `company_id`) VALUES
(1, 1, 1);

--
-- Triggers `section_companies`
--
DROP TRIGGER IF EXISTS `zSectionCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zSectionCompanyBfInsert` BEFORE INSERT ON `section_companies`
 FOR EACH ROW BEGIN
	IF NEW.section_id = "" OR NEW.section_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit_price` double DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `is_default` tinyint(4) DEFAULT '0' COMMENT '0 is normal; 1 is default',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`),
  KEY `filters` (`section_id`,`chart_account_id`),
  KEY `searchs` (`code`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `sys_code`, `company_id`, `section_id`, `code`, `chart_account_id`, `name`, `unit_price`, `description`, `uom_id`, `is_default`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '1b4861137dac2a733b1e2293f100706b', 1, 1, 'S00001', 99, 'Consultation', NULL, '', NULL, 0, '2023-02-21 13:54:47', 1, '2023-02-21 13:54:47', NULL, 1);

--
-- Triggers `services`
--
DROP TRIGGER IF EXISTS `zServiceBfInsert`;
DELIMITER //
CREATE TRIGGER `zServiceBfInsert` BEFORE INSERT ON `services`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.section_id = "" OR NEW.section_id = NULL OR NEW.chart_account_id = "" OR NEW.chart_account_id = NULL OR NEW.code = "" OR NEW.code IS NULL OR NEW.name = "" OR NEW.name IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `services_patient_group_details`
--

CREATE TABLE IF NOT EXISTS `services_patient_group_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `patient_group_id` int(11) DEFAULT NULL,
  `unit_price` float DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `services_patient_group_details`
--

INSERT INTO `services_patient_group_details` (`id`, `service_id`, `patient_group_id`, `unit_price`, `is_active`) VALUES
(1, 1, 1, 1200, 1),
(2, 1, 2, 2400, 1);

-- --------------------------------------------------------

--
-- Table structure for table `services_price_insurances`
--

CREATE TABLE IF NOT EXISTS `services_price_insurances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `services_price_insurance_clons`
--

CREATE TABLE IF NOT EXISTS `services_price_insurance_clons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `services_price_insurance_patient_group_details`
--

CREATE TABLE IF NOT EXISTS `services_price_insurance_patient_group_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `services_price_insurance_id` int(11) DEFAULT NULL,
  `patient_group_id` int(11) DEFAULT NULL,
  `unit_price` float DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `services_price_insurance_patient_group_detail_clone`
--

CREATE TABLE IF NOT EXISTS `services_price_insurance_patient_group_detail_clone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `services_price_insurance_id` int(11) DEFAULT NULL,
  `patient_group_id` int(11) DEFAULT NULL,
  `unit_price` float DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_branches`
--

CREATE TABLE IF NOT EXISTS `service_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `searchs` (`service_id`,`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=99 ;

--
-- Dumping data for table `service_branches`
--

INSERT INTO `service_branches` (`id`, `service_id`, `branch_id`) VALUES
(1, 1, 1),
(98, 1, 1),
(2, 2, 1),
(4, 3, 1),
(5, 4, 1),
(7, 5, 1),
(8, 6, 1),
(9, 7, 1),
(10, 8, 1),
(11, 9, 1),
(27, 10, 1),
(25, 11, 1),
(41, 12, 1),
(15, 13, 1),
(16, 14, 1),
(17, 15, 1),
(18, 16, 1),
(36, 17, 1),
(40, 18, 1),
(21, 19, 1),
(22, 20, 1),
(23, 21, 1),
(24, 22, 1),
(28, 23, 1),
(29, 24, 1),
(30, 25, 1),
(35, 26, 1),
(42, 27, 1),
(58, 28, 1),
(45, 29, 1),
(85, 30, 1),
(95, 31, 1),
(48, 32, 1),
(49, 33, 1),
(65, 34, 1),
(51, 35, 1),
(84, 36, 1),
(53, 37, 1),
(54, 38, 1),
(56, 39, 1),
(83, 40, 1),
(59, 41, 1),
(73, 42, 1),
(74, 43, 1),
(64, 44, 1),
(66, 45, 1),
(70, 46, 1),
(76, 47, 1),
(77, 48, 1),
(78, 49, 1),
(80, 50, 1),
(82, 51, 1),
(86, 52, 1),
(97, 53, 1),
(89, 54, 1),
(90, 55, 1),
(93, 56, 1),
(96, 57, 1);

-- --------------------------------------------------------

--
-- Table structure for table `setting_options`
--

CREATE TABLE IF NOT EXISTS `setting_options` (
  `uom_detail_option` tinyint(4) DEFAULT '0' COMMENT '0: Disable; 1: Enable',
  `calculate_cogs` tinyint(4) DEFAULT '1' COMMENT '1: AVG, 2: FIFO, 3:FILO',
  `shift` tinyint(4) DEFAULT '0' COMMENT '0: Disable; 1: Enable',
  `product_cost_decimal` tinyint(4) DEFAULT '2',
  `allow_delivery` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `setting_options`
--

INSERT INTO `setting_options` (`uom_detail_option`, `calculate_cogs`, `shift`, `product_cost_decimal`, `allow_delivery`) VALUES
(0, 1, 0, 2, 0);

--
-- Triggers `setting_options`
--
DROP TRIGGER IF EXISTS `zSettingOptBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zSettingOptBeforeDelete` BEFORE DELETE ON `setting_options`
 FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete default setting';
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zSettingOptionBfUpdate`;
DELIMITER //
CREATE TRIGGER `zSettingOptionBfUpdate` BEFORE UPDATE ON `setting_options`
 FOR EACH ROW BEGIN
	DECLARE posShift TINYINT(4);
	SELECT COUNT(id) INTO posShift FROM shifts WHERE status = 1 OR status = 2;
	IF posShift > 0 THEN
		SET NEW.shift = 1;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `shift_collect_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `shift_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `total_register` decimal(15,3) DEFAULT NULL,
  `total_register_other` decimal(15,3) DEFAULT NULL,
  `total_sales` decimal(15,3) DEFAULT NULL,
  `total_sales_other` decimal(15,3) DEFAULT NULL,
  `total_acture` decimal(15,3) NOT NULL DEFAULT '0.000',
  `total_acture_other` decimal(15,3) NOT NULL DEFAULT '0.000',
  `total_spread` decimal(15,3) NOT NULL DEFAULT '0.000',
  `register_memo` text COLLATE utf8_unicode_ci,
  `close_shift_memo` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1: Open; 2: Closed; 3: Collected',
  PRIMARY KEY (`id`),
  KEY `times` (`date_start`,`date_end`),
  KEY `filters` (`created_by`,`status`,`shift_collect_id`),
  KEY `company` (`company_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shift_adjusts`
--

CREATE TABLE IF NOT EXISTS `shift_adjusts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) DEFAULT NULL,
  `total_adj` decimal(15,3) NOT NULL DEFAULT '0.000',
  `total_adj_other` decimal(15,3) NOT NULL DEFAULT '0.000',
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shift_collects`
--

CREATE TABLE IF NOT EXISTS `shift_collects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_sales` decimal(15,3) DEFAULT NULL,
  `total_sales_other` decimal(15,3) DEFAULT NULL,
  `total_register` decimal(15,3) DEFAULT NULL,
  `total_register_other` decimal(15,3) DEFAULT NULL,
  `total_adj` decimal(15,3) DEFAULT NULL,
  `total_adj_other` decimal(15,3) DEFAULT NULL,
  `total_cash_collect` decimal(15,3) unsigned DEFAULT NULL,
  `total_cash_collect_other` decimal(15,3) DEFAULT NULL,
  `total_spread` decimal(15,3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filters` (`code`,`date`),
  KEY `searchs` (`company_id`,`branch_id`,`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE IF NOT EXISTS `shipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `shipments`
--
DROP TRIGGER IF EXISTS `zShipmentBfInsert`;
DELIMITER //
CREATE TRIGGER `zShipmentBfInsert` BEFORE INSERT ON `shipments`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `speciment_types`
--

CREATE TABLE IF NOT EXISTS `speciment_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `labo_id` int(11) DEFAULT NULL,
  `labo_item_category_id` int(11) DEFAULT NULL,
  `speciment_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_orders`
--

CREATE TABLE IF NOT EXISTS `stock_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `sales_order_detail_id` int(11) DEFAULT NULL,
  `transfer_order_id` int(11) DEFAULT NULL,
  `purchase_return_id` int(11) DEFAULT NULL,
  `purchase_return_detail_id` int(11) DEFAULT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `consignment_detail_id` int(11) DEFAULT NULL,
  `consignment_return_id` int(11) DEFAULT NULL,
  `vendor_consignment_return_id` int(11) DEFAULT NULL,
  `cycle_product_id` int(11) DEFAULT NULL,
  `inventory_physical_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `qty` decimal(15,3) DEFAULT NULL COMMENT 'Qty As Small Uom',
  PRIMARY KEY (`id`),
  KEY `key_search` (`product_id`,`location_group_id`,`location_id`,`lots_number`,`expired_date`,`date`),
  KEY `sys_code` (`sys_code`),
  KEY `key_filter` (`sales_order_id`,`transfer_order_id`,`purchase_return_id`,`sales_order_detail_id`,`consignment_id`,`consignment_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `streets`
--

CREATE TABLE IF NOT EXISTS `streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sys_code` (`sys_code`),
  KEY `search` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `streets`
--
DROP TRIGGER IF EXISTS `zStreetBfInsert`;
DELIMITER //
CREATE TRIGGER `zStreetBfInsert` BEFORE INSERT ON `streets`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_activities`
--

CREATE TABLE IF NOT EXISTS `system_activities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `act` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bug` mediumtext COLLATE utf8_unicode_ci,
  `browser` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '1: Complete; 2: Bug',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `key_search` (`module`,`act`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `system_activities`
--

INSERT INTO `system_activities` (`id`, `module`, `act`, `bug`, `browser`, `operating_system`, `ip`, `created`, `created_by`, `status`) VALUES
(1, 'Employee', 'Delete', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:42:11', 1, 1),
(2, 'Company', 'Add', '<pre class="cake-debug"><a href="javascript:void(0);" onclick="document.getElementById(''cakeErr1-trace'').style.display = (document.getElementById(''cakeErr1-trace'').style.display == ''none'' ? '''' : ''none'');"><b>Warning</b> (2)</a>: rename(public/company_photo/tmp/', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:48:35', 1, 2),
(3, 'Company', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:48:51', 1, 1),
(4, 'Branch', 'Edit', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:52:42', 1, 1),
(5, 'Company', 'Add', '<pre class="cake-debug"><a href="javascript:void(0);" onclick="document.getElementById(''cakeErr1-trace'').style.display = (document.getElementById(''cakeErr1-trace'').style.display == ''none'' ? '''' : ''none'');"><b>Warning</b> (2)</a>: rename(public/company_photo/tmp/', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:53:07', 1, 2),
(6, 'Employee', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:58:38', 1, 1),
(7, 'Employee', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:59:39', 1, 1),
(8, 'Employee', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:01:32', 1, 1),
(9, 'Employee', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:02:54', 1, 1),
(10, 'Employee', 'Edit', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:31', 1, 1),
(11, 'Employee', 'Edit', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:47', 1, 1),
(12, 'Employee', 'Edit', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:07:05', 1, 1),
(13, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:10:43', 1, 1),
(14, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:11:08', 1, 1),
(15, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:12:03', 1, 1),
(16, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:13:07', 1, 1),
(17, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:13:36', 1, 1),
(18, 'Referral', 'Add', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:14:26', 1, 1),
(19, 'Employee', 'Edit', '', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:16:52', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `term_conditions`
--

CREATE TABLE IF NOT EXISTS `term_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `term_condition_type_id` (`term_condition_type_id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`(255),`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `term_conditions`
--

INSERT INTO `term_conditions` (`id`, `sys_code`, `term_condition_type_id`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'd9e487544c423e64ff9b290487c79a68', 1, 'Can''t change', 'Can''t change', '2019-05-03 16:15:58', 1, '2019-05-03 16:15:58', NULL, 1);

--
-- Triggers `term_conditions`
--
DROP TRIGGER IF EXISTS `zTermConditionBfInsert`;
DELIMITER //
CREATE TRIGGER `zTermConditionBfInsert` BEFORE INSERT ON `term_conditions`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.term_condition_type_id = "" OR NEW.term_condition_type_id = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `term_condition_applies`
--

CREATE TABLE IF NOT EXISTS `term_condition_applies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_type_id` int(11) DEFAULT NULL,
  `term_condition_type_id` int(11) DEFAULT NULL,
  `term_condition_default_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `modules` (`module_type_id`,`term_condition_type_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `term_condition_applies`
--
DROP TRIGGER IF EXISTS `zTermConditionApplyBfInsert`;
DELIMITER //
CREATE TRIGGER `zTermConditionApplyBfInsert` BEFORE INSERT ON `term_condition_applies`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.module_type_id = "" OR NEW.module_type_id = NULL OR NEW.term_condition_type_id = "" OR NEW.term_condition_type_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `term_condition_types`
--

CREATE TABLE IF NOT EXISTS `term_condition_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `term_condition_types`
--

INSERT INTO `term_condition_types` (`id`, `sys_code`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'a927a77b4ae93afd7183ccc9d8dd2dd4', 'Bleak', '2019-05-03 16:15:37', 1, '2019-05-03 16:15:43', 1, 1);

--
-- Triggers `term_condition_types`
--
DROP TRIGGER IF EXISTS `zTermConditionTypeBfInsert`;
DELIMITER //
CREATE TRIGGER `zTermConditionTypeBfInsert` BEFORE INSERT ON `term_condition_types`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `name`, `date`) VALUES
(1, 'Udaya', '2016-10-12 09:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `tmp_ponit_of_sales`
--

CREATE TABLE IF NOT EXISTS `tmp_ponit_of_sales` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `location_group_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_ponit_of_sale_details`
--

CREATE TABLE IF NOT EXISTS `tmp_ponit_of_sale_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tmp_point_of_sale_id` int(11) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `qty_uom_id` bigint(20) DEFAULT NULL,
  `total_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_services`
--

CREATE TABLE IF NOT EXISTS `tmp_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `company_insurance_id` int(11) DEFAULT NULL,
  `queue_id` bigint(20) DEFAULT NULL,
  `queued_doctor_id` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0 = is modifiled ; 1 = not payment ; 2 already payment',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_service_details`
--

CREATE TABLE IF NOT EXISTS `tmp_service_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmp_service_id` int(11) DEFAULT NULL,
  `exchange_rate_id` int(11) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `discount` double DEFAULT '0',
  `unit_price` double DEFAULT '0',
  `total_price` double DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tobacco_alcohols`
--

CREATE TABLE IF NOT EXISTS `tobacco_alcohols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queued_id` int(11) DEFAULT NULL,
  `queued_doctor_id` int(11) DEFAULT NULL,
  `tob_achol` int(11) DEFAULT NULL COMMENT '1 = NO ; 2 = T ; 3 = A',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1' COMMENT '1 = active ; 2 = delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracks`
--

CREATE TABLE IF NOT EXISTS `tracks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `description` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `val` date DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `is_recalculate` tinyint(4) NOT NULL DEFAULT '0',
  `is_recalculate_process` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tracks`
--

INSERT INTO `tracks` (`id`, `pid`, `description`, `val`, `date_start`, `date_end`, `is_recalculate`, `is_recalculate_process`) VALUES
(1, NULL, 'Recalculate Average Cost', '1969-12-31', '2017-11-07 11:31:36', '2017-11-07 11:31:36', 1, 0);

--
-- Triggers `tracks`
--
DROP TRIGGER IF EXISTS `zTrackBeforeDelete`;
DELIMITER //
CREATE TRIGGER `zTrackBeforeDelete` BEFORE DELETE ON `tracks`
 FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete default setting';
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zTrackBeforeInsert`;
DELIMITER //
CREATE TRIGGER `zTrackBeforeInsert` BEFORE INSERT ON `tracks`
 FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot insert default setting';
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zTrackBfUpdate`;
DELIMITER //
CREATE TRIGGER `zTrackBfUpdate` BEFORE UPDATE ON `tracks`
 FOR EACH ROW BEGIN
	IF OLD.val < NEW.val AND NEW.is_recalculate = 1 THEN
		SET NEW.val = OLD.val;
		SET NEW.is_recalculate = 1;
   END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_orders`
--

CREATE TABLE IF NOT EXISTS `transfer_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_stock_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `from_location_group_id` int(11) DEFAULT NULL,
  `to_location_group_id` int(11) DEFAULT NULL,
  `to_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `fulfillment_date` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `type` tinyint(4) DEFAULT '1' COMMENT '1: Transfer; 2: Consignment',
  `approved` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `is_approve` tinyint(4) DEFAULT '1' COMMENT '0: Confirm Approval; 1: Approved; 2: Reject',
  `is_process` tinyint(4) NOT NULL DEFAULT '0',
  `error` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from_location_id` (`from_location_group_id`),
  KEY `to_location_id` (`to_location_group_id`),
  KEY `to_code` (`to_code`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_order_details`
--

CREATE TABLE IF NOT EXISTS `transfer_order_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transfer_order_id` bigint(20) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location_from_id` int(11) DEFAULT NULL,
  `location_to_id` int(11) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_order_id` (`transfer_order_id`),
  KEY `product_id` (`product_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_receives`
--

CREATE TABLE IF NOT EXISTS `transfer_receives` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transfer_receive_result_id` bigint(20) DEFAULT NULL,
  `transfer_order_id` bigint(20) DEFAULT NULL,
  `transfer_order_detail_id` bigint(20) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_order_id` (`transfer_order_id`),
  KEY `transfer_order_detail_id` (`transfer_order_detail_id`),
  KEY `product_id` (`product_id`),
  KEY `transfer_receive_result_id` (`transfer_receive_result_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_receive_results`
--

CREATE TABLE IF NOT EXISTS `transfer_receive_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transfer_order_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_order_id` (`transfer_order_id`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_uses`
--

CREATE TABLE IF NOT EXISTS `treatment_uses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `treatment_uses`
--

INSERT INTO `treatment_uses` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '១​ដង ក្នុង ​១​ថ្ងៃ', '2018-12-31 10:24:33', 1, '2022-10-20 09:43:38', 1, 1),
(2, '២​ដង ក្នុង ១​ ថ្ងៃ', '2018-12-31 10:24:39', 1, '2022-10-20 09:43:21', 1, 1),
(3, '៣​ដង ក្នុង​១​ថ្ងៃ', '2018-12-31 10:25:02', 1, '2022-10-20 09:44:00', 1, 1),
(4, 'A', '2019-05-03 16:22:47', 1, '2019-05-03 16:22:47', NULL, 1),
(5, '1 time 1 day', '2022-10-20 09:44:51', 1, '2022-10-20 09:44:51', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `type_payments`
--

CREATE TABLE IF NOT EXISTS `type_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `type_payments`
--

INSERT INTO `type_payments` (`id`, `name`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'Cash', '2016-09-02 16:19:26', 1, NULL, NULL, 1),
(2, 'Bank', '2016-09-02 16:19:49', 1, NULL, NULL, 1),
(3, 'Cheque', '2016-09-02 16:19:49', 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `uoms`
--

CREATE TABLE IF NOT EXISTS `uoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbr` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `uoms`
--

INSERT INTO `uoms` (`id`, `sys_code`, `type`, `name`, `abbr`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '4bf0dc8d50b10f05f459f21f0b16e975', 'Count', 'Bottle', 'Bottle', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1),
(2, '6d26bba1de9d265b6ae60be4ffe7e7d2', 'Count', 'Tablet', 'Tablet', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1),
(3, 'c8f57d9fdd3f0b92839544900fcb5176', 'Count', 'box', 'box', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1),
(4, '253f88243fe4b00a251094c78c0d475c', 'Count', 'Tube', 'Tube', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1),
(5, '3245afd803b8e5ee459ee8f305e225cb', 'Count', 'Sachet', 'Sachet', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1),
(6, '85632c0e7c5036988a321266dda5ea29', 'Count', 'Capsule', 'Capsule', NULL, '2023-02-09 14:44:46', 1, NULL, NULL, 1);

--
-- Triggers `uoms`
--
DROP TRIGGER IF EXISTS `zUomBfInsert`;
DELIMITER //
CREATE TRIGGER `zUomBfInsert` BEFORE INSERT ON `uoms`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.type = "" OR NEW.type = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.abbr = "" OR NEW.abbr = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zUomBfUpdate`;
DELIMITER //
CREATE TRIGGER `zUomBfUpdate` BEFORE UPDATE ON `uoms`
 FOR EACH ROW BEGIN
	IF NEW.type = "" OR NEW.type IS NULL OR NEW.name = "" OR NEW.name IS NULL OR NEW.abbr = "" OR NEW.abbr IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `uom_conversions`
--

CREATE TABLE IF NOT EXISTS `uom_conversions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `from_uom_id` int(11) DEFAULT NULL,
  `to_uom_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_small_uom` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `from_uom_id` (`from_uom_id`),
  KEY `to_uom_id` (`to_uom_id`),
  KEY `is_small_uom` (`is_small_uom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `uom_conversions`
--
DROP TRIGGER IF EXISTS `zUomConversionBfInsert`;
DELIMITER //
CREATE TRIGGER `zUomConversionBfInsert` BEFORE INSERT ON `uom_conversions`
 FOR EACH ROW BEGIN
	IF NEW.from_uom_id = "" OR NEW.from_uom_id IS NULL OR NEW.to_uom_id = "" OR NEW.to_uom_id IS NULL OR NEW.value = "" OR NEW.value IS NULL OR NEW.value < 0 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `zUomConversionBfUpdate`;
DELIMITER //
CREATE TRIGGER `zUomConversionBfUpdate` BEFORE UPDATE ON `uom_conversions`
 FOR EACH ROW BEGIN
	DECLARE isCheck INT(11);
	SELECT COUNT(id) INTO isCheck FROM products WHERE id IN (SELECT product_id FROM inventory_totals) AND price_uom_id = OLD.from_uom_id AND is_active = 1;
	IF isCheck > 0 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot update or delete this data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `upload_slides`
--

CREATE TABLE IF NOT EXISTS `upload_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `caption` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uroflowmetry_services`
--

CREATE TABLE IF NOT EXISTS `uroflowmetry_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uroflowmetry_service_request_id` int(11) NOT NULL,
  `uroflowmetry_service_queue_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uroflowmetry_service_images`
--

CREATE TABLE IF NOT EXISTS `uroflowmetry_service_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uroflowmetry_service_id` int(11) DEFAULT NULL,
  `src_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uroflowmetry_service_requests`
--

CREATE TABLE IF NOT EXISTS `uroflowmetry_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `uroflowmetry_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uroflowmetry_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `uroflowmetry_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `uroflowmetry_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pin` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_project_id` bigint(20) DEFAULT NULL,
  `project_id` bigint(20) DEFAULT NULL,
  `user_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_start` datetime DEFAULT NULL,
  `session_active` datetime DEFAULT NULL,
  `session_lat` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_long` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_accuracy` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_attempt` datetime DEFAULT NULL,
  `login_attempt_remote_ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_attempt_http_user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_lat` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_long` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_accuracy` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expired` date DEFAULT NULL,
  `duration` bigint(20) DEFAULT '0',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `telephone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `signature_photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_hash` tinyint(4) DEFAULT '0',
  `is_sync` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  `room_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `sys_code`, `pin`, `main_project_id`, `project_id`, `user_code`, `session_id`, `session_start`, `session_active`, `session_lat`, `session_long`, `session_accuracy`, `login_attempt`, `login_attempt_remote_ip`, `login_attempt_http_user_agent`, `login_lat`, `login_long`, `login_accuracy`, `expired`, `duration`, `username`, `password`, `first_name`, `last_name`, `sex`, `dob`, `address`, `telephone`, `email`, `nationality`, `signature_photo`, `created`, `created_by`, `modified`, `modified_by`, `is_hash`, `is_sync`, `is_active`, `room_id`) VALUES
(1, '1377e4a99f76f3a1f8f0bccb7ead773f', NULL, NULL, NULL, NULL, 'dr78ut1drgg32jehj40iehhhq4', '2023-06-07 15:48:04', '2023-06-07 15:48:53', '', '', '', NULL, '127.0.0.1', 'OS: Windows 10 Browser: Gecko based', NULL, NULL, NULL, '2025-08-10', 0, 'admin', '$2a$10$60uCVW6aKktXHMmtHDwV0OCWuQpzUNN24k/evF8uqhKbH6HCPWvn6', 'admin', '', 'Male', '1991-06-01', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 15:48:04', 1, 1, 0, 1, NULL),
(2, '681c4141e7ade6f8762f6704b82261ef', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'reaksmey', '$2a$10$ZU/pglnJUOurHRfey0ew2.XO45kaSkiJRalouT5v2MkUixPJP/TpW', 'Ly', 'Reaksmey', 'Female', '1986-12-02', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 12:05:12', 1, 1, 0, 1, NULL),
(3, '7e715ab811348175783b1b9f919c95bc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'pharmacy', '$2a$10$GfdjH/SjOym.N1koBXJqtev7UYa9eRdClMLJ866vQPVO8yZlvpugW', 'Chan', 'Mony', 'Female', '1993-09-05', 'Sleng Rolearng Vilage Khan Sen Sok Phnom Penh', '010 27 19 33', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 12:05:54', 1, 1, 0, 1, NULL),
(4, '4d52eb718d0dc3a1ab65839ab8164f78', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'danin', '$2a$10$irCuzGxh6No9SNw8U4JQ8e7/hUppmpksvHM4LQPoq1bEHmj12ASwy', 'Chhun ', 'Danin', 'Female', '2005-03-08', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 12:08:30', 1, 1, 0, 1, NULL),
(5, 'ba19afbc2d4151997eb5232a5f659cef', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'malina', '$2a$10$CX5L1X6I3NAYEp9TfS2gLefiLfD5or6b/LeDpuJCqj2a6v3OerUji', 'Vong', 'Malina', 'Female', '2000-06-13', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 12:10:06', 1, 1, 0, 1, NULL),
(6, '19b0df236d66ec5d3a872d53a1597ad0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'mony', '$2a$10$Qb2yTK5aJDhSdnvTw2gB5.P3QrR13e03bxYbGxby9yTsQOBDoTKtm', 'Chan', 'Mony', 'Male', '2001-01-11', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2023-06-07 12:16:09', 1, 1, 0, 1, NULL),
(7, '23a089f85d3ecd7e749e80a1c646b99e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'nurse', '$2a$10$NWy.j.mTmjI37B7h3BAI2.FdHd0ottWWzPck/rZiVfoHsJI0.0klq', 'User', '7', 'Female', '1997-01-01', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2020-09-14 14:42:04', 2, 1, 0, 1, NULL),
(8, '1cccbb1ee146cc078f9c260c4996bc23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'adminlabo', '$2a$10$FLVGynDXdARCLVBlcjLKie.uisO.OhJJZpxlS5K3y9m.EIy35Y1Zy', 'User', '8', 'Male', '0000-00-00', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2022-12-15 18:11:38', 1, 1, 0, 1, NULL),
(9, '6f8f9d5ee780cded31be5387748a3381', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'doctor7', '$2a$10$k3MFuNKlFjMn/FgEGhbA4OOMvBtmg1vKNj6lfBrvVOUijv7QpN2g6', 'User', '9', 'Male', '2000-03-01', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2020-06-24 10:19:46', 11, 1, 0, 1, NULL),
(10, '8a650e4c41f19b6e96a2a66c7e56e592', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-10', 0, 'doctor8', '$2a$10$JecHdpKEkGPxiz2HG4oI1uMDQMg1pEZ36uZ/ldYrApyqGLtssjWgK', 'User', '10', 'Male', '2000-05-01', '', '', '', 36, NULL, '2017-02-17 09:33:11', 1, '2021-01-05 12:46:32', 1, 1, 0, 1, NULL);

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `zUserBfUpdate`;
DELIMITER //
CREATE TRIGGER `zUserBfUpdate` BEFORE INSERT ON `users`
 FOR EACH ROW BEGIN
	IF NEW.first_name = "" OR NEW.first_name = NULL OR NEW.last_name = "" OR NEW.last_name = NULL OR NEW.username = "" OR NEW.username = NULL OR NEW.password = "" OR NEW.password = NULL OR NEW.expired = "" OR NEW.expired = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tbl_from_id` bigint(20) DEFAULT NULL,
  `tbl_to_id` bigint(20) DEFAULT NULL,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `browser` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `key_search` (`type`,`tbl_from_id`,`tbl_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=69 ;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`id`, `user_id`, `type`, `tbl_from_id`, `tbl_to_id`, `action`, `browser`, `operating_system`, `ip`, `created`) VALUES
(1, 1, 'Login', 1, 0, 'Login', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:27:03'),
(2, 1, 'Referral', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:39:59'),
(3, 1, 'User', 2, 0, 'LogOut', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:41:10'),
(4, 1, 'Login', 3, 0, 'Login', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:41:16'),
(5, 1, 'Employee', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:41:57'),
(6, 1, 'Employee', 1, 0, 'Delete', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:42:11'),
(7, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:42:13'),
(8, 1, 'Employee', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:43:48'),
(9, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:43:48'),
(10, 1, 'Company', 0, 0, 'Dashborad', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:45:46'),
(11, 1, 'Company', 1, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:45:48'),
(12, 1, 'Company', 1, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:48:35'),
(13, 1, 'Company', 1, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:48:44'),
(14, 1, 'Company', 1, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:48:50'),
(15, 1, 'Branch', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:49:05'),
(16, 1, 'Branch', 1, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:51:02'),
(17, 1, 'Company', 1, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:51:15'),
(18, 1, 'Company', 1, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:52:06'),
(19, 1, 'Branch', 1, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:52:42'),
(20, 1, 'Company', 1, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:53:07'),
(21, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:53:49'),
(22, 1, 'Login', 4, 0, 'Login', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:56:53'),
(23, 1, 'Employee', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:57:26'),
(24, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:57:27'),
(25, 1, 'Employee', 1, 0, 'Save Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:58:38'),
(26, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:58:40'),
(27, 1, 'Employee', 2, 0, 'Save Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:59:39'),
(28, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 11:59:41'),
(29, 1, 'Employee', 3, 0, 'Save Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:01:32'),
(30, 1, 'Employee', 0, 0, 'Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:01:34'),
(31, 1, 'Employee', 4, 0, 'Save Add New', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:02:53'),
(32, 1, 'User', 2, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:03:52'),
(33, 1, 'User', 2, 2, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:04:25'),
(34, 1, 'User', 2, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:04:32'),
(35, 1, 'User', 2, 2, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:04:48'),
(36, 1, 'User', 2, 0, 'Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:04:53'),
(37, 1, 'User', 2, 0, 'Save Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:05:12'),
(38, 1, 'User', 3, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:05:20'),
(39, 1, 'User', 3, 3, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:05:54'),
(40, 1, 'User', 4, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:02'),
(41, 1, 'Employee', 3, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:23'),
(42, 1, 'Employee', 3, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:31'),
(43, 1, 'Employee', 3, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:35'),
(44, 1, 'Employee', 3, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:46'),
(45, 1, 'Employee', 3, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:06:50'),
(46, 1, 'Employee', 3, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:07:05'),
(47, 1, 'Employee', 3, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:07:13'),
(48, 1, 'User', 4, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:07:24'),
(49, 1, 'User', 4, 4, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:08:04'),
(50, 1, 'User', 4, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:08:08'),
(51, 1, 'User', 4, 0, 'Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:08:14'),
(52, 1, 'User', 4, 0, 'Save Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:08:30'),
(53, 1, 'User', 5, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:08:37'),
(54, 1, 'User', 5, 5, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:09:39'),
(55, 1, 'User', 5, 0, 'Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:09:48'),
(56, 1, 'User', 5, 0, 'Save Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:10:06'),
(57, 1, 'Referral', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:10:19'),
(58, 1, 'User', 6, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:14:49'),
(59, 1, 'User', 6, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:15:00'),
(60, 1, 'User', 6, 6, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:15:13'),
(61, 1, 'User', 6, 0, 'Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:15:46'),
(62, 1, 'User', 6, 0, 'Save Edit Profile', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:16:09'),
(63, 1, 'Employee', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:16:44'),
(64, 1, 'Employee', 2, 0, 'Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:16:48'),
(65, 1, 'Employee', 2, 0, 'Save Edit', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 12:16:52'),
(66, 1, 'Referral', 0, 0, 'Dashboard', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 15:47:41'),
(67, 1, 'User', 5, 0, 'LogOut', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 15:47:57'),
(68, 1, 'Login', 6, 0, 'Login', 'Gecko based', 'Windows 10', '127.0.0.1', '2023-06-07 15:48:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_branches`
--

CREATE TABLE IF NOT EXISTS `user_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_branch_id` (`user_id`,`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=336 ;

--
-- Dumping data for table `user_branches`
--

INSERT INTO `user_branches` (`id`, `user_id`, `branch_id`) VALUES
(320, 1, 1),
(331, 2, 1),
(332, 3, 1),
(333, 4, 1),
(334, 5, 1),
(335, 6, 1),
(326, 7, 1),
(327, 8, 1),
(328, 9, 1),
(329, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_cgroups`
--

CREATE TABLE IF NOT EXISTS `user_cgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cgroup_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cgroup_id_user_id` (`cgroup_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_companies`
--

CREATE TABLE IF NOT EXISTS `user_companies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_company_id` (`user_id`,`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=369 ;

--
-- Dumping data for table `user_companies`
--

INSERT INTO `user_companies` (`id`, `user_id`, `company_id`) VALUES
(353, 1, 1),
(364, 2, 1),
(365, 3, 1),
(366, 4, 1),
(367, 5, 1),
(368, 6, 1),
(359, 7, 1),
(360, 8, 1),
(361, 9, 1),
(362, 10, 1);

--
-- Triggers `user_companies`
--
DROP TRIGGER IF EXISTS `zUserCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zUserCompanyBfInsert` BEFORE INSERT ON `user_companies`
 FOR EACH ROW BEGIN
	IF NEW.user_id = "" OR NEW.user_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_dashboards`
--

CREATE TABLE IF NOT EXISTS `user_dashboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `display` tinyint(4) DEFAULT NULL COMMENT '1: Show; 2: Hide',
  `auto_refresh` tinyint(4) DEFAULT NULL COMMENT '1: Off; 2: Auto',
  `time_refresh` int(11) DEFAULT NULL COMMENT 'Calculate As Second',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_module_id` (`user_id`,`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=270 ;

--
-- Dumping data for table `user_dashboards`
--

INSERT INTO `user_dashboards` (`id`, `user_id`, `module_id`, `display`, `auto_refresh`, `time_refresh`, `created`, `modified`) VALUES
(1, 1, 509, 2, 1, 5, '2018-12-26 10:18:43', '2019-01-07 09:25:04'),
(2, 1, 499, 2, 1, 5, '2018-12-26 10:18:43', '2019-01-07 09:24:50'),
(3, 1, 510, 2, 1, 30, '2018-12-26 10:18:43', '2019-01-07 09:25:05'),
(4, 1, 613, 2, 1, 30, '2018-12-26 10:18:44', '2019-01-07 09:25:04'),
(5, 1, 602, 2, 1, 30, '2018-12-26 10:18:44', '2019-01-07 09:25:04'),
(6, 1, 614, 2, 1, 30, '2018-12-26 10:18:44', '2019-01-07 09:24:46'),
(7, 1, 611, 2, 1, 30, '2018-12-26 10:18:44', '2019-03-06 10:24:50'),
(8, 1, 612, 2, 1, 30, '2018-12-26 10:18:44', '2019-01-07 09:24:47'),
(9, 1, 615, 2, 1, 30, '2018-12-26 10:18:44', '2019-01-07 09:24:52'),
(10, 1, 509, 1, 1, 5, '2018-12-26 10:22:15', '2018-12-26 10:22:15'),
(11, 1, 509, 1, 1, 5, '2018-12-26 10:55:59', '2018-12-26 10:55:59'),
(12, 1, 509, 1, 1, 5, '2018-12-26 13:58:46', '2018-12-26 13:58:46'),
(13, 1, 509, 1, 1, 5, '2019-01-07 08:57:06', '2019-01-07 08:57:06'),
(14, 1, 509, 1, 1, 5, '2019-01-07 09:12:42', '2019-01-07 09:12:42'),
(15, 1, 509, 1, 1, 5, '2019-01-07 09:24:34', '2019-01-07 09:24:34'),
(16, 2, 602, 2, 1, 30, '2019-01-08 11:47:24', '2019-01-08 11:47:37'),
(17, 2, 499, 2, 1, 5, '2019-01-08 11:47:24', '2019-01-08 11:47:36'),
(18, 2, 510, 2, 1, 30, '2019-01-08 11:47:24', '2019-01-08 11:47:38'),
(19, 2, 611, 2, 1, 5, '2019-01-08 11:47:24', '2019-01-08 11:47:38'),
(20, 2, 613, 2, 1, 5, '2019-01-08 11:47:24', '2019-01-08 11:47:35'),
(21, 2, 612, 1, 1, 30, '2019-01-08 11:47:24', '2019-01-08 11:47:24'),
(22, 2, 509, 2, 1, 30, '2019-01-08 11:47:24', '2019-01-08 11:47:37'),
(23, 2, 615, 2, 1, 5, '2019-01-08 11:47:24', '2019-01-08 11:47:38'),
(24, 2, 614, 2, 1, 5, '2019-01-08 11:47:25', '2019-01-08 11:47:37'),
(25, 2, NULL, 2, 1, 5, '2019-01-08 11:47:35', '2019-01-08 11:47:35'),
(26, 8, 509, 2, 1, 30, '2019-01-08 13:07:09', '2019-01-08 13:08:04'),
(27, 8, 510, 2, 1, 30, '2019-01-08 13:07:09', '2019-01-08 13:08:04'),
(28, 10, 509, 1, 1, 5, '2019-01-19 08:27:33', '2019-01-19 08:27:33'),
(29, 10, 499, 1, 1, 5, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(30, 10, 611, 1, 1, 30, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(31, 10, 510, 1, 1, 5, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(32, 10, 602, 1, 1, 30, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(33, 10, 613, 1, 1, 30, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(34, 10, 615, 1, 1, 30, '2019-01-19 08:27:34', '2019-01-19 08:27:34'),
(35, 10, 612, 1, 1, 30, '2019-01-19 08:27:35', '2019-01-19 08:27:35'),
(36, 10, 614, 1, 1, 30, '2019-01-19 08:27:35', '2019-01-19 08:27:35'),
(37, 5, 499, 1, 1, 5, '2019-02-21 13:00:35', '2019-02-21 13:00:35'),
(38, 5, 602, 1, 1, 30, '2019-02-21 13:00:35', '2019-02-21 16:51:57'),
(39, 5, 611, 1, 1, 30, '2019-02-21 13:00:35', '2019-02-21 13:00:35'),
(40, 5, 612, 1, 1, 30, '2019-02-21 13:00:36', '2019-02-21 13:00:36'),
(41, 5, 613, 1, 1, 30, '2019-02-21 13:00:36', '2019-02-21 13:00:36'),
(42, 5, 614, 1, 1, 30, '2019-02-21 13:00:36', '2019-02-21 16:51:56'),
(43, 5, 615, 1, 1, 30, '2019-02-21 13:00:36', '2019-02-21 13:00:36'),
(44, 1, 509, 1, 1, 5, '2019-02-21 22:52:25', '2019-02-21 22:52:25'),
(45, 11, 510, 1, 1, 5, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(46, 11, 499, 1, 1, 5, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(47, 11, 602, 1, 1, 30, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(48, 11, 509, 1, 1, 5, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(49, 11, 611, 1, 1, 30, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(50, 11, 613, 1, 1, 30, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(51, 11, 614, 1, 1, 30, '2019-03-07 16:33:56', '2019-03-07 16:33:56'),
(52, 11, 612, 1, 1, 30, '2019-03-07 16:33:57', '2019-03-07 16:33:57'),
(53, 11, 615, 1, 1, 30, '2019-03-07 16:33:57', '2019-03-07 16:33:57'),
(54, 11, 509, 1, 1, 5, '2019-03-07 16:36:12', '2019-03-07 16:36:12'),
(55, 11, 509, 1, 1, 5, '2019-03-07 16:37:51', '2019-03-07 16:37:51'),
(56, 11, 509, 1, 1, 5, '2019-03-08 08:49:57', '2019-03-08 08:49:57'),
(57, 11, 509, 1, 1, 5, '2019-03-08 08:50:27', '2019-03-08 08:50:27'),
(58, 11, 509, 1, 1, 5, '2019-03-08 10:33:42', '2019-03-08 10:33:42'),
(59, 11, 509, 1, 1, 5, '2019-03-08 10:38:04', '2019-03-08 10:38:04'),
(60, 11, 509, 1, 1, 5, '2019-03-08 13:28:26', '2019-03-08 13:28:26'),
(61, 11, 509, 1, 1, 5, '2019-03-08 13:30:17', '2019-03-08 13:30:17'),
(62, 11, 509, 1, 1, 5, '2019-03-08 15:49:48', '2019-03-08 15:49:48'),
(63, 11, 509, 1, 1, 5, '2019-03-08 16:28:36', '2019-03-08 16:28:36'),
(64, 11, 509, 1, 1, 5, '2019-03-11 11:52:25', '2019-03-11 11:52:25'),
(65, 11, 509, 1, 1, 5, '2019-03-11 11:56:47', '2019-03-11 11:56:47'),
(66, 11, 509, 1, 1, 5, '2019-03-11 12:02:16', '2019-03-11 12:02:16'),
(67, 11, 509, 1, 1, 5, '2019-03-12 15:04:31', '2019-03-12 15:04:31'),
(68, 11, 509, 1, 1, 5, '2019-03-12 15:06:51', '2019-03-12 15:06:51'),
(69, 11, 509, 1, 1, 5, '2019-03-12 15:08:53', '2019-03-12 15:08:53'),
(70, 11, 509, 1, 1, 5, '2019-03-12 15:10:11', '2019-03-12 15:10:11'),
(71, 11, 509, 1, 1, 5, '2019-03-12 15:11:17', '2019-03-12 15:11:17'),
(72, 11, 509, 1, 1, 5, '2019-03-14 17:39:23', '2019-03-14 17:39:23'),
(73, 11, 509, 1, 1, 5, '2019-03-14 17:44:23', '2019-03-14 17:44:23'),
(74, 11, 509, 1, 1, 5, '2019-03-14 18:04:17', '2019-03-14 18:04:17'),
(75, 11, 509, 1, 1, 5, '2019-03-18 16:08:46', '2019-03-18 16:08:46'),
(76, 11, 509, 1, 1, 5, '2019-03-19 16:20:34', '2019-03-19 16:20:34'),
(77, 11, 509, 1, 1, 5, '2019-03-22 09:51:55', '2019-03-22 09:51:55'),
(78, 11, 509, 1, 1, 5, '2019-03-22 10:33:46', '2019-03-22 10:33:46'),
(79, 11, 509, 1, 1, 5, '2019-03-22 11:26:48', '2019-03-22 11:26:48'),
(80, 11, 509, 1, 1, 5, '2019-03-28 10:01:07', '2019-03-28 10:01:07'),
(81, 11, 509, 1, 1, 5, '2019-03-28 13:54:44', '2019-03-28 13:54:44'),
(82, 11, 509, 1, 1, 5, '2019-03-28 14:09:04', '2019-03-28 14:09:04'),
(83, 11, 509, 1, 1, 5, '2019-03-30 09:36:54', '2019-03-30 09:36:54'),
(84, 11, 509, 1, 1, 5, '2019-03-30 09:37:35', '2019-03-30 09:37:35'),
(85, 11, 509, 1, 1, 5, '2019-04-05 12:52:08', '2019-04-05 12:52:08'),
(86, 11, 509, 1, 1, 5, '2019-04-05 12:53:50', '2019-04-05 12:53:50'),
(87, 11, 509, 1, 1, 5, '2019-04-27 11:24:50', '2019-04-27 11:24:50'),
(88, 11, 509, 1, 1, 5, '2019-05-03 16:12:43', '2019-05-03 16:12:43'),
(89, 11, 509, 1, 1, 5, '2019-05-04 09:11:05', '2019-05-04 09:11:05'),
(90, 11, 509, 1, 1, 5, '2019-05-04 09:12:22', '2019-05-04 09:12:22'),
(91, 11, 509, 1, 1, 5, '2019-05-04 09:14:08', '2019-05-04 09:14:08'),
(92, 11, 509, 1, 1, 5, '2019-05-04 09:21:23', '2019-05-04 09:21:23'),
(93, 11, 509, 1, 1, 5, '2019-05-06 08:31:53', '2019-05-06 08:31:53'),
(94, 11, 509, 1, 1, 5, '2019-05-06 08:33:45', '2019-05-06 08:33:45'),
(95, 11, 509, 1, 1, 5, '2019-05-06 08:34:06', '2019-05-06 08:34:06'),
(96, 12, 509, 1, 1, 5, '2019-05-06 08:34:42', '2019-05-06 08:34:42'),
(97, 12, 611, 1, 1, 30, '2019-05-06 08:34:42', '2019-05-06 08:34:42'),
(98, 12, 602, 1, 1, 30, '2019-05-06 08:34:42', '2019-05-06 08:34:42'),
(99, 12, 510, 1, 1, 5, '2019-05-06 08:34:42', '2019-05-06 08:34:42'),
(100, 12, 612, 1, 1, 30, '2019-05-06 08:34:42', '2019-05-06 08:34:42'),
(101, 12, 613, 1, 1, 30, '2019-05-06 08:34:43', '2019-05-06 08:34:43'),
(102, 12, 499, 1, 1, 5, '2019-05-06 08:34:43', '2019-05-06 08:34:43'),
(103, 12, 615, 1, 1, 30, '2019-05-06 08:34:43', '2019-05-06 08:34:43'),
(104, 12, 614, 1, 1, 30, '2019-05-06 08:34:43', '2019-05-06 08:34:43'),
(105, 7, 499, 1, 1, 5, '2019-05-06 09:31:49', '2019-05-06 09:31:49'),
(106, 7, 602, 1, 1, 30, '2019-05-06 09:31:49', '2019-05-06 09:31:49'),
(107, 7, 509, 1, 1, 5, '2019-05-06 09:31:49', '2019-05-06 09:31:49'),
(108, 7, 612, 1, 1, 30, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(109, 7, 613, 1, 1, 30, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(110, 7, 510, 1, 1, 5, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(111, 7, 611, 1, 1, 30, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(112, 7, 614, 1, 1, 30, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(113, 7, 615, 1, 1, 30, '2019-05-06 09:31:50', '2019-05-06 09:31:50'),
(114, 7, 509, 1, 1, 5, '2019-05-06 09:36:51', '2019-05-06 09:36:51'),
(115, 7, 509, 1, 1, 5, '2019-05-06 10:18:34', '2019-05-06 10:18:34'),
(116, 7, 509, 1, 1, 5, '2019-05-06 10:20:26', '2019-05-06 10:20:26'),
(117, 11, 509, 1, 1, 5, '2019-05-06 15:29:25', '2019-05-06 15:29:25'),
(118, 11, 509, 1, 1, 5, '2019-05-06 15:33:12', '2019-05-06 15:33:12'),
(119, 11, 509, 1, 1, 5, '2019-05-06 15:42:38', '2019-05-06 15:42:38'),
(120, 11, 509, 1, 1, 5, '2019-05-06 18:10:24', '2019-05-06 18:10:24'),
(121, 11, 509, 1, 1, 5, '2019-05-07 15:29:36', '2019-05-07 15:29:36'),
(122, 11, 509, 1, 1, 5, '2019-05-07 15:35:47', '2019-05-07 15:35:47'),
(123, 11, 509, 1, 1, 5, '2019-05-07 15:36:12', '2019-05-07 15:36:12'),
(124, 11, 509, 1, 1, 5, '2019-05-07 15:41:33', '2019-05-07 15:41:33'),
(125, 11, 509, 1, 1, 5, '2019-05-07 16:11:41', '2019-05-07 16:11:41'),
(126, 11, 509, 1, 1, 5, '2019-05-07 17:23:33', '2019-05-07 17:23:33'),
(127, 11, 509, 1, 1, 5, '2019-05-07 17:33:08', '2019-05-07 17:33:08'),
(128, 11, 509, 1, 1, 5, '2019-05-07 18:05:46', '2019-05-07 18:05:46'),
(129, 11, 509, 1, 1, 5, '2019-05-07 18:08:43', '2019-05-07 18:08:43'),
(130, 11, 509, 1, 1, 5, '2019-05-07 18:11:56', '2019-05-07 18:11:56'),
(131, 7, 509, 1, 1, 5, '2019-05-08 09:49:46', '2019-05-08 09:49:46'),
(132, 7, 509, 1, 1, 5, '2019-05-08 11:22:19', '2019-05-08 11:22:19'),
(133, 11, 509, 1, 1, 5, '2019-05-08 14:18:33', '2019-05-08 14:18:33'),
(134, 7, 509, 1, 1, 5, '2019-05-08 14:53:58', '2019-05-08 14:53:58'),
(135, 7, 509, 1, 1, 5, '2019-05-08 15:25:59', '2019-05-08 15:25:59'),
(136, 7, 509, 1, 1, 5, '2019-05-08 15:44:34', '2019-05-08 15:44:34'),
(137, 7, 509, 1, 1, 5, '2019-05-08 15:47:34', '2019-05-08 15:47:34'),
(138, 7, 509, 1, 1, 5, '2019-05-08 15:49:57', '2019-05-08 15:49:57'),
(139, 11, 509, 1, 1, 5, '2019-05-08 15:57:28', '2019-05-08 15:57:28'),
(140, 7, 509, 1, 1, 5, '2019-05-08 15:58:43', '2019-05-08 15:58:43'),
(141, 7, 509, 1, 1, 5, '2019-05-08 16:02:47', '2019-05-08 16:02:47'),
(142, 7, 509, 1, 1, 5, '2019-05-08 16:09:13', '2019-05-08 16:09:13'),
(143, 7, 509, 1, 1, 5, '2019-05-08 16:10:20', '2019-05-08 16:10:20'),
(144, 7, 509, 1, 1, 5, '2019-05-08 16:27:35', '2019-05-08 16:27:35'),
(145, 7, 509, 1, 1, 5, '2019-05-08 16:38:24', '2019-05-08 16:38:24'),
(146, 7, 509, 1, 1, 5, '2019-05-08 17:40:25', '2019-05-08 17:40:25'),
(147, 7, 509, 1, 1, 5, '2019-05-09 08:01:44', '2019-05-09 08:01:44'),
(148, 7, 509, 1, 1, 5, '2019-05-09 08:48:43', '2019-05-09 08:48:43'),
(149, 7, 509, 1, 1, 5, '2019-05-09 09:11:42', '2019-05-09 09:11:42'),
(150, 7, 509, 1, 1, 5, '2019-05-09 11:49:32', '2019-05-09 11:49:32'),
(151, 7, 509, 1, 1, 5, '2019-05-09 11:50:11', '2019-05-09 11:50:11'),
(152, 7, 509, 1, 1, 5, '2019-05-09 11:50:36', '2019-05-09 11:50:36'),
(153, 7, 509, 1, 1, 5, '2019-05-09 11:51:04', '2019-05-09 11:51:04'),
(154, 7, 509, 1, 1, 5, '2019-05-09 13:52:04', '2019-05-09 13:52:04'),
(155, 7, 509, 1, 1, 5, '2019-05-09 14:27:21', '2019-05-09 14:27:21'),
(156, 7, 509, 1, 1, 5, '2019-05-09 14:44:20', '2019-05-09 14:44:20'),
(157, 7, 509, 1, 1, 5, '2019-05-09 14:50:59', '2019-05-09 14:50:59'),
(158, 7, 509, 1, 1, 5, '2019-05-09 14:54:15', '2019-05-09 14:54:15'),
(159, 7, 509, 1, 1, 5, '2019-05-09 15:06:36', '2019-05-09 15:06:36'),
(160, 7, 509, 1, 1, 5, '2019-05-10 08:12:29', '2019-05-10 08:12:29'),
(161, 7, 509, 1, 1, 5, '2019-05-10 08:18:12', '2019-05-10 08:18:12'),
(162, 7, 509, 1, 1, 5, '2019-05-10 08:19:23', '2019-05-10 08:19:23'),
(163, 7, 509, 1, 1, 5, '2019-05-10 08:22:44', '2019-05-10 08:22:44'),
(164, 7, 509, 1, 1, 5, '2019-05-10 13:36:39', '2019-05-10 13:36:39'),
(165, 7, 509, 1, 1, 5, '2019-05-10 13:44:10', '2019-05-10 13:44:10'),
(166, 7, 509, 1, 1, 5, '2019-05-10 13:51:29', '2019-05-10 13:51:29'),
(167, 7, 509, 1, 1, 5, '2019-05-10 14:09:17', '2019-05-10 14:09:17'),
(168, 7, 509, 1, 1, 5, '2019-05-11 08:17:28', '2019-05-11 08:17:28'),
(169, 7, 509, 1, 1, 5, '2019-05-11 08:57:42', '2019-05-11 08:57:42'),
(170, 7, 509, 1, 1, 5, '2019-05-11 11:20:36', '2019-05-11 11:20:36'),
(171, 7, 509, 1, 1, 5, '2019-05-11 11:34:16', '2019-05-11 11:34:16'),
(172, 7, 509, 1, 1, 5, '2019-05-11 11:37:14', '2019-05-11 11:37:14'),
(173, 7, 509, 1, 1, 5, '2019-05-11 11:38:27', '2019-05-11 11:38:27'),
(174, 7, 509, 1, 1, 5, '2019-05-11 11:45:20', '2019-05-11 11:45:20'),
(175, 7, 509, 1, 1, 5, '2019-05-11 12:05:08', '2019-05-11 12:05:08'),
(176, 7, 509, 1, 1, 5, '2019-05-11 12:22:16', '2019-05-11 12:22:16'),
(177, 7, 509, 1, 1, 5, '2019-05-11 16:48:25', '2019-05-11 16:48:25'),
(178, 7, 509, 1, 1, 5, '2019-05-11 16:49:16', '2019-05-11 16:49:16'),
(179, 7, 509, 1, 1, 5, '2019-05-16 08:21:50', '2019-05-16 08:21:50'),
(180, 7, 509, 1, 1, 5, '2019-05-16 09:51:54', '2019-05-16 09:51:54'),
(181, 7, 509, 1, 1, 5, '2019-05-16 09:52:16', '2019-05-16 09:52:16'),
(182, 7, 509, 1, 1, 5, '2019-05-16 09:53:10', '2019-05-16 09:53:10'),
(183, 7, 509, 1, 1, 5, '2019-05-16 10:12:14', '2019-05-16 10:12:14'),
(184, 7, 509, 1, 1, 5, '2019-05-16 10:14:03', '2019-05-16 10:14:03'),
(185, 7, 509, 1, 1, 5, '2019-05-16 15:04:59', '2019-05-16 15:04:59'),
(186, 7, 509, 1, 1, 5, '2019-05-16 16:01:37', '2019-05-16 16:01:37'),
(187, 7, 509, 1, 1, 5, '2019-05-16 17:34:55', '2019-05-16 17:34:55'),
(188, 7, 509, 1, 1, 5, '2019-05-16 17:37:49', '2019-05-16 17:37:49'),
(189, 7, 509, 1, 1, 5, '2019-05-16 17:45:41', '2019-05-16 17:45:41'),
(190, 7, 509, 1, 1, 5, '2019-05-16 17:48:59', '2019-05-16 17:48:59'),
(191, 7, 509, 1, 1, 5, '2019-05-17 10:22:42', '2019-05-17 10:22:42'),
(192, 7, 509, 1, 1, 5, '2019-05-17 10:23:58', '2019-05-17 10:23:58'),
(193, 7, 509, 1, 1, 5, '2019-05-17 16:28:36', '2019-05-17 16:28:36'),
(194, 7, 509, 1, 1, 5, '2019-05-17 16:42:18', '2019-05-17 16:42:18'),
(195, 7, 509, 1, 1, 5, '2019-05-17 16:46:02', '2019-05-17 16:46:02'),
(196, 7, 509, 1, 1, 5, '2019-05-17 16:49:32', '2019-05-17 16:49:32'),
(197, 7, 509, 1, 1, 5, '2019-05-17 16:49:41', '2019-05-17 16:49:41'),
(198, 7, 509, 1, 1, 5, '2019-05-17 16:56:02', '2019-05-17 16:56:02'),
(199, 7, 509, 1, 1, 5, '2019-05-21 16:27:00', '2019-05-21 16:27:00'),
(200, 7, 509, 1, 1, 5, '2019-05-27 08:14:41', '2019-05-27 08:14:41'),
(201, 7, 509, 1, 1, 5, '2019-05-27 11:44:08', '2019-05-27 11:44:08'),
(202, 7, 509, 1, 1, 5, '2019-05-29 14:44:08', '2019-05-29 14:44:08'),
(203, 7, 509, 1, 1, 5, '2019-05-30 17:51:13', '2019-05-30 17:51:13'),
(204, 7, 509, 1, 1, 5, '2019-05-31 09:52:08', '2019-05-31 09:52:08'),
(205, 7, 509, 1, 1, 5, '2019-06-06 08:10:15', '2019-06-06 08:10:15'),
(206, 7, 509, 1, 1, 5, '2019-06-06 08:27:53', '2019-06-06 08:27:53'),
(207, 7, 509, 1, 1, 5, '2019-06-06 11:49:54', '2019-06-06 11:49:54'),
(208, 7, 509, 1, 1, 5, '2019-06-19 11:59:53', '2019-06-19 11:59:53'),
(209, 7, 509, 1, 1, 5, '2019-06-27 13:35:09', '2019-06-27 13:35:09'),
(210, 7, 509, 1, 1, 5, '2019-06-27 13:35:50', '2019-06-27 13:35:50'),
(211, 7, 509, 1, 1, 5, '2019-06-27 13:42:31', '2019-06-27 13:42:31'),
(212, 7, 509, 1, 1, 5, '2019-07-09 08:23:17', '2019-07-09 08:23:17'),
(213, 7, 509, 1, 1, 5, '2019-07-09 08:26:40', '2019-07-09 08:26:40'),
(214, 7, 509, 1, 1, 5, '2019-07-09 14:10:13', '2019-07-09 14:10:13'),
(215, 7, 509, 1, 1, 5, '2019-07-10 14:04:31', '2019-07-10 14:04:31'),
(216, 7, 509, 1, 1, 5, '2019-07-15 08:06:47', '2019-07-15 08:06:47'),
(217, 7, 509, 1, 1, 5, '2019-07-16 10:32:06', '2019-07-16 10:32:06'),
(218, 7, 509, 1, 1, 5, '2019-07-17 15:22:57', '2019-07-17 15:22:57'),
(219, 7, 509, 1, 1, 5, '2019-07-24 08:49:54', '2019-07-24 08:49:54'),
(220, 7, 509, 1, 1, 5, '2019-07-24 09:45:19', '2019-07-24 09:45:19'),
(221, 7, 509, 1, 1, 5, '2019-08-04 14:49:18', '2019-08-04 14:49:18'),
(222, 7, 509, 1, 1, 5, '2019-08-04 16:47:59', '2019-08-04 16:47:59'),
(223, 7, 509, 1, 1, 5, '2019-08-04 16:48:27', '2019-08-04 16:48:27'),
(224, 7, 509, 1, 1, 5, '2019-08-04 16:48:44', '2019-08-04 16:48:44'),
(225, 7, 509, 1, 1, 5, '2019-08-04 16:49:57', '2019-08-04 16:49:57'),
(226, 14, 499, 2, 1, 5, '2019-08-05 08:51:59', '2019-12-30 13:46:41'),
(227, 14, 602, 2, 1, 30, '2019-08-05 08:52:00', '2019-12-30 13:46:42'),
(228, 14, 612, 1, 1, 30, '2019-08-05 08:52:00', '2019-08-05 08:52:00'),
(229, 14, 613, 2, 1, 30, '2019-08-05 08:52:00', '2019-12-30 13:46:40'),
(230, 14, 509, 2, 1, 5, '2019-08-05 08:52:00', '2019-12-30 13:46:42'),
(231, 14, 510, 2, 1, 30, '2019-08-05 08:52:00', '2019-12-30 13:46:44'),
(232, 14, 614, 2, 1, 30, '2019-08-05 08:52:01', '2019-12-30 13:46:42'),
(233, 14, 611, 2, 1, 30, '2019-08-05 08:52:01', '2019-12-30 13:46:43'),
(234, 14, 615, 2, 1, 30, '2019-08-05 08:52:01', '2019-12-30 13:46:43'),
(235, 14, 509, 1, 1, 5, '2019-08-05 08:52:25', '2019-08-05 08:52:25'),
(236, 14, 509, 1, 1, 5, '2019-08-05 08:53:22', '2019-08-05 08:53:22'),
(237, 7, 509, 1, 1, 5, '2019-08-05 13:41:15', '2019-08-05 13:41:15'),
(238, 7, 509, 1, 1, 5, '2019-08-05 13:41:44', '2019-08-05 13:41:44'),
(239, 7, 509, 1, 1, 5, '2019-08-05 13:44:15', '2019-08-05 13:44:15'),
(240, 14, 509, 1, 1, 5, '2019-08-05 13:45:37', '2019-08-05 13:45:37'),
(241, 7, 509, 1, 1, 5, '2019-08-05 13:51:18', '2019-08-05 13:51:18'),
(242, 7, 509, 1, 1, 5, '2019-08-05 16:46:01', '2019-08-05 16:46:01'),
(243, 14, 509, 1, 1, 5, '2019-08-05 17:37:50', '2019-08-05 17:37:50'),
(244, 14, 509, 1, 1, 5, '2019-08-07 14:50:45', '2019-08-07 14:50:45'),
(245, 14, 509, 1, 1, 5, '2019-08-07 14:57:14', '2019-08-07 14:57:14'),
(246, 14, 509, 1, 1, 5, '2019-08-07 14:58:03', '2019-08-07 14:58:03'),
(247, 14, 509, 1, 1, 5, '2019-08-07 14:59:39', '2019-08-07 14:59:39'),
(248, 14, 509, 1, 1, 5, '2019-08-08 12:49:26', '2019-08-08 12:49:26'),
(249, 14, 509, 1, 1, 5, '2019-08-08 16:32:36', '2019-08-08 16:32:36'),
(250, 14, 509, 1, 1, 5, '2019-08-12 08:16:01', '2019-08-12 08:16:01'),
(251, 14, 509, 1, 1, 5, '2019-08-13 14:45:44', '2019-08-13 14:45:44'),
(252, 14, 509, 1, 1, 5, '2019-08-13 14:57:59', '2019-08-13 14:57:59'),
(253, 14, 509, 1, 1, 5, '2019-08-15 13:19:45', '2019-08-15 13:19:45'),
(254, 14, 509, 1, 1, 5, '2019-08-22 09:44:30', '2019-08-22 09:44:30'),
(255, 14, 509, 1, 1, 5, '2019-11-28 08:22:28', '2019-11-28 08:22:28'),
(256, 14, 509, 1, 1, 5, '2019-11-28 08:47:28', '2019-11-28 08:47:28'),
(257, 14, 509, 1, 1, 5, '2019-11-28 09:03:23', '2019-11-28 09:03:23'),
(258, 14, 509, 1, 1, 5, '2019-12-17 15:55:55', '2019-12-17 15:55:55'),
(259, 14, 509, 1, 1, 5, '2019-12-17 16:09:24', '2019-12-17 16:09:24'),
(260, 14, 509, 1, 1, 5, '2019-12-17 16:45:41', '2019-12-17 16:45:41'),
(261, 14, 509, 1, 1, 5, '2019-12-17 17:02:55', '2019-12-17 17:02:55'),
(262, 14, 509, 1, 1, 5, '2019-12-17 17:10:04', '2019-12-17 17:10:04'),
(263, 14, 509, 1, 1, 5, '2019-12-28 12:13:56', '2019-12-28 12:13:56'),
(264, 14, 509, 1, 1, 5, '2019-12-28 12:23:30', '2019-12-28 12:23:30'),
(265, 14, 509, 1, 1, 5, '2019-12-28 12:28:43', '2019-12-28 12:28:43'),
(266, 14, 509, 1, 1, 5, '2019-12-28 12:29:14', '2019-12-28 12:29:14'),
(267, 14, 509, 1, 1, 5, '2019-12-30 13:36:03', '2019-12-30 13:36:03'),
(268, 14, 509, 1, 1, 5, '2019-12-30 13:46:31', '2019-12-30 13:46:31'),
(269, 14, NULL, 2, 1, 30, '2019-12-30 13:46:41', '2019-12-30 13:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_employees`
--

CREATE TABLE IF NOT EXISTS `user_employees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `user_employees`
--

INSERT INTO `user_employees` (`id`, `user_id`, `employee_id`) VALUES
(2, 2, 2),
(3, 3, 1),
(4, 4, 3),
(5, 5, 4),
(6, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_group_id` (`user_id`,`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=215 ;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `user_id`, `group_id`) VALUES
(192, 1, 1),
(11, 1, 2),
(203, 2, 2),
(202, 2, 4),
(194, 3, 1),
(206, 4, 4),
(207, 4, 5),
(204, 4, 7),
(205, 4, 8),
(210, 5, 4),
(211, 5, 5),
(208, 5, 7),
(209, 5, 8),
(214, 6, 2),
(212, 6, 7),
(213, 6, 8),
(198, 7, 1),
(199, 8, 1),
(200, 9, 1),
(201, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_introduces`
--

CREATE TABLE IF NOT EXISTS `user_introduces` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `introduce_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`introduce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE IF NOT EXISTS `user_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_location_id` (`user_id`,`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- Dumping data for table `user_locations`
--

INSERT INTO `user_locations` (`id`, `user_id`, `location_id`) VALUES
(1, 1, 1),
(13, 1, 2),
(2, 2, 1),
(12, 2, 2),
(3, 3, 1),
(22, 3, 2),
(23, 4, 1),
(24, 4, 2),
(11, 5, 1),
(21, 5, 2),
(4, 6, 1),
(15, 6, 2),
(5, 7, 1),
(16, 7, 2),
(6, 8, 1),
(17, 8, 2),
(7, 9, 1),
(18, 9, 2),
(8, 10, 1),
(14, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_location_groups`
--

CREATE TABLE IF NOT EXISTS `user_location_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `location_group_id` (`location_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=85 ;

--
-- Dumping data for table `user_location_groups`
--

INSERT INTO `user_location_groups` (`id`, `user_id`, `location_group_id`) VALUES
(35, 9, 1),
(36, 8, 1),
(44, 10, 1),
(51, 8, 2),
(52, 9, 2),
(53, 10, 2),
(63, 7, 2),
(65, 7, 1),
(71, 1, 2),
(72, 1, 1),
(75, 2, 2),
(76, 2, 1),
(77, 3, 1),
(78, 3, 2),
(79, 4, 2),
(80, 4, 1),
(81, 5, 1),
(82, 5, 2),
(83, 6, 1),
(84, 6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE IF NOT EXISTS `user_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `http_user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remote_addr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `user_id`, `type`, `http_user_agent`, `remote_addr`, `created`) VALUES
(1, 1, 'Login', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 11:27:03'),
(2, 1, 'LogOut', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 11:41:10'),
(3, 1, 'Login', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 11:41:16'),
(4, 1, 'Login', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 11:56:53'),
(5, 1, 'LogOut', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 15:47:57'),
(6, 1, 'Login', 'OS: Windows 10 Browser: Gecko based', '127.0.0.1', '2023-06-07 15:48:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_pgroups`
--

CREATE TABLE IF NOT EXISTS `user_pgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pgroup_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_pgroup_id` (`user_id`,`pgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_print_product`
--

CREATE TABLE IF NOT EXISTS `user_print_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filters` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_shares`
--

CREATE TABLE IF NOT EXISTS `user_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module_type_id` int(11) DEFAULT NULL,
  `share_option` tinyint(4) DEFAULT NULL,
  `share_users` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `share_except_users` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_searchs` (`module_type_id`,`share_option`,`share_users`(255),`share_except_users`(255)),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ut_api_auth_tokens`
--

CREATE TABLE IF NOT EXISTS `ut_api_auth_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `auth_token` varchar(250) DEFAULT NULL,
  `auth_signature` varchar(250) DEFAULT NULL,
  `last_logged` datetime DEFAULT NULL,
  `expired_in` datetime DEFAULT NULL,
  `is_logged` char(1) DEFAULT '0',
  `is_expired` char(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `auth` (`auth_token`,`auth_signature`),
  KEY `status` (`is_logged`,`is_expired`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vat_modules`
--

CREATE TABLE IF NOT EXISTS `vat_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vat_setting_id` int(11) DEFAULT NULL,
  `apply_to` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `key_search` (`vat_setting_id`,`apply_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `vat_modules`
--

INSERT INTO `vat_modules` (`id`, `vat_setting_id`, `apply_to`, `created`, `created_by`, `is_active`) VALUES
(1, 1, 34, '2017-11-15 19:04:11', 1, 2),
(2, 1, 25, '2017-11-15 19:04:11', 1, 2),
(3, 1, 40, '2017-11-15 19:04:11', 1, 2),
(4, 2, 21, '2017-11-15 19:04:28', 1, 1),
(5, 2, 41, '2017-11-15 19:04:28', 1, 1),
(6, 3, 25, '2019-05-03 16:17:05', 1, 2),
(7, 1, 34, '2019-05-06 09:44:07', 7, 1),
(8, 1, 40, '2019-05-06 09:44:07', 7, 1),
(9, 1, 25, '2019-05-06 09:44:07', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vat_settings`
--

CREATE TABLE IF NOT EXISTS `vat_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1: Sales; 2: Purchase',
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vat_percent` decimal(6,3) DEFAULT NULL,
  `chart_account_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '1: Active; 2: Deactive',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`),
  KEY `searchs` (`name`,`chart_account_id`,`is_active`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `vat_settings`
--

INSERT INTO `vat_settings` (`id`, `sys_code`, `company_id`, `type`, `name`, `vat_percent`, `chart_account_id`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '45df3021379594cf6b1d95b19e61d463', 1, 1, 'None', '0.000', 102, '2017-07-21 13:41:05', 1, '2019-05-06 09:44:07', 7, 1),
(2, '7836a7a5b933b31a04eda437428ada3f', 1, 2, 'None', '0.000', 104, '2017-07-21 13:41:33', 1, '2017-11-15 19:04:28', 1, 1),
(3, '83d4ffe1019a93a95a2dfc1b096fa994', 1, 1, '10%', '10.000', 102, '2019-05-03 16:17:05', 1, '2019-05-06 09:34:28', 7, 1);

--
-- Triggers `vat_settings`
--
DROP TRIGGER IF EXISTS `zVatSettingBfInsert`;
DELIMITER //
CREATE TRIGGER `zVatSettingBfInsert` BEFORE INSERT ON `vat_settings`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code IS NULL OR NEW.company_id = "" OR NEW.company_id IS NULL OR NEW.`type` = "" OR NEW.`type` IS NULL OR NEW.name = "" OR NEW.name IS NULL OR NEW.vat_percent IS NULL OR NEW.chart_account_id = "" OR NEW.chart_account_id IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE IF NOT EXISTS `vendors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `payment_term_id` int(11) DEFAULT NULL,
  `photo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vendor_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `work_telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `vendor_code` (`vendor_code`),
  KEY `sys_code` (`sys_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `sys_code`, `province_id`, `district_id`, `commune_id`, `village_id`, `payment_term_id`, `photo`, `vendor_code`, `name`, `country_id`, `work_telephone`, `other_number`, `fax_number`, `email_address`, `address`, `note`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, 'fe5d156a7b595c5b0894b1d86ca4ae6c', NULL, NULL, NULL, NULL, 1, NULL, '19V0000001', 'General', NULL, '', NULL, '', '', 'PP', NULL, '2019-02-20 11:32:53', 1, '2019-02-20 11:32:53', NULL, 1),
(2, '7544e00a458cf345fc0faf054eb51d10', NULL, NULL, NULL, NULL, 1, '', '19V0000002', 'Sok Nim', 36, '093332866', '', '', '', '', '', '2019-05-03 16:03:24', 1, '2019-05-03 16:03:24', NULL, 1);

--
-- Triggers `vendors`
--
DROP TRIGGER IF EXISTS `zVendorBfInsert`;
DELIMITER //
CREATE TRIGGER `zVendorBfInsert` BEFORE INSERT ON `vendors`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.payment_term_id = "" OR NEW.payment_term_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_companies`
--

CREATE TABLE IF NOT EXISTS `vendor_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vendor_companies`
--

INSERT INTO `vendor_companies` (`id`, `vendor_id`, `company_id`) VALUES
(1, 1, 1),
(2, 2, 1);

--
-- Triggers `vendor_companies`
--
DROP TRIGGER IF EXISTS `zVendorCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zVendorCompanyBfInsert` BEFORE INSERT ON `vendor_companies`
 FOR EACH ROW BEGIN
	IF NEW.vendor_id = "" OR NEW.vendor_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_consignments`
--

CREATE TABLE IF NOT EXISTS `vendor_consignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `currency_center_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1: Edit; 0: Void; 1: Issued; 2: Fullfiled',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filters` (`date`,`code`,`status`),
  KEY `filter2` (`location_group_id`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_consignment_details`
--

CREATE TABLE IF NOT EXISTS `vendor_consignment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_consignment_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `date_expired` date DEFAULT NULL,
  `qty` decimal(15,2) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `unit_cost` decimal(15,3) DEFAULT '0.000',
  `total_cost` decimal(15,3) DEFAULT '0.000',
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `vendor_consignment_id` (`vendor_consignment_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_consignment_returns`
--

CREATE TABLE IF NOT EXISTS `vendor_consignment_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vendor_consignment_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `location_group_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '-1: Edit; 0: Void; 1: Issued; 2: Fullfiled',
  PRIMARY KEY (`id`),
  KEY `company` (`company_id`,`branch_id`),
  KEY `filters` (`date`,`code`,`status`),
  KEY `filter2` (`location_group_id`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_consignment_return_details`
--

CREATE TABLE IF NOT EXISTS `vendor_consignment_return_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_consignment_return_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `default_order` decimal(15,2) DEFAULT NULL,
  `lots_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0',
  `date_expired` date DEFAULT NULL,
  `qty` decimal(15,2) DEFAULT NULL,
  `qty_uom_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `vendor_consignment_return_id` (`vendor_consignment_return_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_contacts`
--

CREATE TABLE IF NOT EXISTS `vendor_contacts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `title` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_telephone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `modules` (`company_id`,`vendor_id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`contact_name`,`is_active`,`contact_telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `vendor_contacts`
--
DROP TRIGGER IF EXISTS `zVendorContactBfInsert`;
DELIMITER //
CREATE TRIGGER `zVendorContactBfInsert` BEFORE INSERT ON `vendor_contacts`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.company_id = "" OR NEW.company_id = NULL OR NEW.vendor_id = "" OR NEW.vendor_id = NULL OR NEW.contact_name = "" OR NEW.contact_name = NULL OR NEW.sex = "" OR NEW.sex = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_vgroups`
--

CREATE TABLE IF NOT EXISTS `vendor_vgroups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint(20) DEFAULT NULL,
  `vgroup_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `vgroup_id` (`vgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vendor_vgroups`
--

INSERT INTO `vendor_vgroups` (`id`, `vendor_id`, `vgroup_id`) VALUES
(1, 1, 1),
(2, 2, 1);

--
-- Triggers `vendor_vgroups`
--
DROP TRIGGER IF EXISTS `zVendorVgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zVendorVgroupBfInsert` BEFORE INSERT ON `vendor_vgroups`
 FOR EACH ROW BEGIN
	IF NEW.vendor_id = "" OR NEW.vendor_id = NULL OR NEW.vgroup_id = "" OR NEW.vgroup_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vgroups`
--

CREATE TABLE IF NOT EXISTS `vgroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vgroups`
--

INSERT INTO `vgroups` (`id`, `sys_code`, `name`, `description`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) VALUES
(1, '91ee0d5079177efeb29e3a3ccec09dd2', 'General', NULL, '2019-02-20 11:32:40', 1, '2019-02-20 11:32:40', NULL, 1),
(2, '0bf72ae0de6721247abc9d6b4eb04a09', 'Genaral1', NULL, '2019-05-03 16:12:04', 1, '2019-05-03 16:12:04', NULL, 1);

--
-- Triggers `vgroups`
--
DROP TRIGGER IF EXISTS `zVgroupBfInsert`;
DELIMITER //
CREATE TRIGGER `zVgroupBfInsert` BEFORE INSERT ON `vgroups`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vgroup_companies`
--

CREATE TABLE IF NOT EXISTS `vgroup_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vgroup_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vgroup_id` (`vgroup_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vgroup_companies`
--

INSERT INTO `vgroup_companies` (`id`, `vgroup_id`, `company_id`) VALUES
(1, 1, 1),
(2, 2, 1);

--
-- Triggers `vgroup_companies`
--
DROP TRIGGER IF EXISTS `zVgroupCompanyBfInsert`;
DELIMITER //
CREATE TRIGGER `zVgroupCompanyBfInsert` BEFORE INSERT ON `vgroup_companies`
 FOR EACH ROW BEGIN
	IF NEW.vgroup_id = "" OR NEW.vgroup_id = NULL OR NEW.company_id = "" OR NEW.company_id = NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `villages`
--

CREATE TABLE IF NOT EXISTS `villages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commune_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sys_code` (`sys_code`),
  KEY `commune_id` (`commune_id`),
  KEY `searchs` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Triggers `villages`
--
DROP TRIGGER IF EXISTS `zVillagesBfInsert`;
DELIMITER //
CREATE TRIGGER `zVillagesBfInsert` BEFORE INSERT ON `villages`
 FOR EACH ROW BEGIN
	IF NEW.sys_code = "" OR NEW.sys_code = NULL OR NEW.name = "" OR NEW.name = NULL OR NEW.commune_id = "" OR NEW.commune_id = NULL THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid Data';
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_services`
--

CREATE TABLE IF NOT EXISTS `xray_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `xray_service_queue_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conclusion` text COLLATE utf8_unicode_ci,
  `xray_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_service_images`
--

CREATE TABLE IF NOT EXISTS `xray_service_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `xray_srv_id` int(11) DEFAULT NULL,
  `src_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_service_requests`
--

CREATE TABLE IF NOT EXISTS `xray_service_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `xray_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_service_request_updates`
--

CREATE TABLE IF NOT EXISTS `xray_service_request_updates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_service_request_id` int(11) DEFAULT NULL,
  `xray_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_ray_consultation_images`
--

CREATE TABLE IF NOT EXISTS `x_ray_consultation_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `x_ray_service_consultation_id` int(11) DEFAULT NULL,
  `dir_file` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_ray_service_consultations`
--

CREATE TABLE IF NOT EXISTS `x_ray_service_consultations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `x_ray_service_queue_id` int(11) DEFAULT NULL,
  `service_de_provenant` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `doctor_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `consultation` text COLLATE utf8_unicode_ci,
  `echo_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_ray_service_details`
--

CREATE TABLE IF NOT EXISTS `x_ray_service_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `service_description` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_ray_service_queues`
--

CREATE TABLE IF NOT EXISTS `x_ray_service_queues` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
