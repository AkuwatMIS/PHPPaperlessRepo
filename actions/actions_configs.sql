-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2019 at 11:08 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paperless`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions_configs`
--

CREATE TABLE `actions_configs` (
  `id` int(11) NOT NULL,
  `parent_type` varchar(20) NOT NULL,
  `parent_table` varchar(20) NOT NULL,
  `flow` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `actions_configs`
--

INSERT INTO `actions_configs` (`id`, `parent_type`, `parent_table`, `flow`, `sort_order`, `project_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'application', 'application_actions', 'family_member_info,social_appraisal,business_appraisal', 1, 0, 2, 0, 1542016890, 1542016890),
(2, 'appraisal', 'application_actions', 'approved/rejected', 2, 0, 2, 0, 1542016890, 1542016890),
(3, 'verification', 'application_actions', 'group_formation', 3, 0, 2, 0, 1542016890, 1542016890),
(4, 'group', 'group_actions', 'lac', 4, 0, 2, 0, 1542016890, 1542016890),
(5, 'loan', 'loan_actions', 'loan_approved,lac,	cheque_printing,fund_request,takaful,	disbursement', 5, 0, 2, 0, 1542016890, 1542016890),
(6, 'application', 'application_actions', 'family_member_info,social_appraisal', 1, 1, 2, 0, 1542016890, 1542016890),
(7, 'appraisal', 'application_actions', 'approved/rejected', 2, 1, 2, 0, 1542016890, 1542016890),
(8, 'verification', 'application_actions', 'group_formation', 3, 1, 2, 0, 1542016890, 1542016890),
(9, 'group', 'group_actions', 'lac', 4, 1, 2, 0, 1542016890, 1542016890),
(10, 'loan', 'loan_actions', 'loan_approved,lac,	cheque_printing,fund_request,takaful,	disbursement', 5, 1, 2, 0, 1542016890, 1542016890);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions_configs`
--
ALTER TABLE `actions_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_type`),
  ADD KEY `user_id` (`parent_table`),
  ADD KEY `action` (`flow`),
  ADD KEY `status` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions_configs`
--
ALTER TABLE `actions_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
