-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2019 at 07:57 AM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.2.14-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paperless_etl`
--

--
-- Dumping data for table `appraisals`
--

INSERT INTO `appraisals` (`id`, `name`, `appraisal_table`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'social_appraisal', 'appraisals_social', 0, 0, 0, 0, 0),
(2, 'business_appraisal', 'appraisals_business', 0, 0, 0, 0, 0);

--
-- Dumping data for table `config_rules`
--

INSERT INTO `config_rules` (`id`, `group`, `priority`, `key`, `value`, `operator`, `operator_desc`, `parent_type`, `parent_id`, `project_id`, `field_name`) VALUES
(1, 'groups', 0, 'max_size', '6', '<=', 'less than or equal to', 'global', 0, 0, 'group_size'),
(2, 'groups', 0, 'min_size', '3', '>=', 'greater than or equal to', 'global', 0, 0, 'group_size'),
(3, 'groups', 2, 'max_size', '15', '<=', 'less than or equal to', 'project', 0, 0, 'group_size'),
(4, 'groups', 2, 'max_size', '9', '<=', 'less than or equal to', 'project', 0, 0, 'group_size'),
(5, 'members', 0, 'min_age', '18', '>=', 'greater than or equal to', 'global', 0, 0, 'age'),
(6, 'members', 0, 'max_age', '62', '<=', 'less than or equal to', 'global', 0, 0, 'age'),
(7, 'applications', 0, 'fee', '200', '==', 'equal to', 'global', 0, 0, 'fee'),
(8, 'applications', 2, 'fee', '0', '==', 'equal to', 'project', 2, 0, 'fee'),
(9, 'applications', 1, 'max_req_amount', '20000', '<=', 'less than or equal to', 'product', 1, 0, 'req_amount'),
(10, 'applications', 1, 'min_req_amount', '500', '>=', 'greater than or equal to', 'product', 1, 0, 'req_amount'),
(11, 'applications', 0, 'max_req_amount', '50000', '<=', 'less than or equal to', 'global', 0, 0, 'req_amount'),
(12, 'applications', 0, 'min_req_amount', '10000', '>=', 'greater than or equal to', 'global', 0, 0, 'req_amount'),
(13, 'loans', 0, 'max_installment', '20', '<=', 'less than or equal to', 'global', 0, 0, 'inst_months'),
(14, 'loans', 0, 'min_installment', '3', '>=', 'greater than or equal to', 'global', 0, 0, 'inst_months'),
(15, 'members', 3, 'min_age', '12', '>=', 'greater than or equal to', 'region', 4, 0, 'age'),
(16, 'members', 3, 'max_age', '43', '<=', 'less than or equal to', 'region', 4, 0, 'age');

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `module_type`, `module_id`, `parent_type`, `name`, `is_required`) VALUES
(1, 'applications', 0, NULL, 'utility_bill', 1),
(2, 'applications', 0, NULL, 'marriage_certificate', 0),
(3, 'projects', 17, 'applications', 'education_certificate', 1);

--
-- Dumping data for table `mobile_permissions`
--

INSERT INTO `mobile_permissions` (`id`, `role`, `mobile_screen_id`, `permission`, `deleted`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'AA', 1, 1, 0, 1, 1, 1526118548, 1514973866),
(2, 'AA', 2, 1, 0, 1, 1, 1526118548, 1514973866),
(4, 'ADMIN', 1, 1, 0, 1, 0, 1538567420, 1538567420),
(7, 'ADMIN', 4, 1, 0, 1, 0, 1538567422, 1538567422),
(14, 'ADMIN', 2, 1, 0, 1, 0, 1538648168, 1538648168),
(15, 'ADMIN', 3, 1, 0, 1, 0, 1538648168, 1538648168),
(16, 'ADMIN', 5, 1, 0, 1, 0, 1538648168, 1538648168),
(17, 'ADMIN', 6, 1, 0, 1, 0, 1538648168, 1538648168),
(18, 'ADMIN', 7, 1, 0, 1, 0, 1538648168, 1538648168),
(19, 'ADMIN', 8, 1, 0, 1, 0, 1538648168, 1538648168),
(20, 'ADMIN', 9, 1, 0, 1, 0, 1538648168, 1538648168),
(21, 'ADMIN', 10, 1, 0, 1, 0, 1538648168, 1538648168),
(22, 'ADMIN', 11, 1, 0, 1, 0, 1538648168, 1538648168),
(28, 'AM', 6, 1, 0, 1, 0, 1539672189, 1539672189),
(29, 'LO', 1, 1, 0, 1, 0, 1539673944, 1539673944),
(30, 'LO', 2, 1, 0, 1, 0, 1539673944, 1539673944),
(31, 'LO', 3, 1, 0, 1, 0, 1539673944, 1539673944),
(32, 'DEO', 1, 1, 0, 1, 0, 1539673962, 1539673962),
(33, 'DEO', 2, 1, 0, 1, 0, 1539673962, 1539673962),
(34, 'DEO', 3, 1, 0, 1, 0, 1539673962, 1539673962),
(35, 'BM', 4, 1, 0, 1, 0, 1539674017, 1539674017),
(36, 'BM', 5, 1, 0, 1, 0, 1539674017, 1539674017),
(41, 'BM', 10, 1, 0, 1, 0, 1539674017, 1539674017),
(42, 'BM', 11, 1, 0, 1, 0, 1539674017, 1539674017),
(43, 'AM', 7, 1, 0, 1, 0, 1539674110, 1539674110),
(44, 'AM', 9, 1, 0, 1, 0, 1539674110, 1539674110),
(45, 'AM', 10, 1, 0, 1, 0, 1539674110, 1539674110),
(46, 'DEO', 10, 1, 0, 1, 0, 1539674120, 1539674120),
(47, 'DEO', 11, 1, 0, 1, 0, 1539674120, 1539674120),
(53, 'RM', 10, 1, 0, 1, 0, 1540189133, 1540189133),
(55, 'RM', 3, 1, 0, 1, 0, 1540189222, 1540189222),
(71, 'LO', 11, 1, 0, 1, 0, 1540463869, 1540463869),
(72, 'LO', 10, 1, 0, 1, 0, 1540464886, 1540464886),
(73, 'BM', 8, 1, 0, 1, 0, 1540466278, 1540466278),
(74, 'ADMIN', 12, 1, 0, 1, 0, 1540471568, 1540471568),
(75, 'ADMIN', 13, 1, 0, 1, 0, 1540471568, 1540471568),
(76, 'ADMIN', 14, 1, 0, 1, 0, 1540471568, 1540471568),
(77, 'ADMIN', 15, 1, 0, 1, 0, 1540471568, 1540471568),
(82, 'BM', 12, 1, 0, 1, 0, 1540471608, 1540471608),
(83, 'BM', 14, 1, 0, 1, 0, 1540471608, 1540471608),
(85, 'LO', 13, 1, 0, 1, 0, 1540471699, 1540471699),
(87, 'LO', 15, 1, 0, 1, 0, 1540471699, 1540471699),
(88, 'AM', 16, 1, 0, 1, 0, 1539674110, 1539674110),
(90, 'AM', 8, 1, 0, 1, 0, 1548778433, 1548778433),
(91, 'AM', 11, 1, 0, 1, 0, 1548778589, 1548778589),
(92, 'RM', 14, 1, 0, 1, 0, 1548832888, 1548832888),
(94, 'RM', 17, 1, 0, 1, 0, 1548833755, 1548833755);

--
-- Dumping data for table `mobile_screens`
--

INSERT INTO `mobile_screens` (`id`, `name`, `deleted`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'SEARCH_MEMBER', 0, 1, 1, 1526118548, 1514973866),
(2, 'MEMBERS', 0, 1, 1, 1526118548, 1526118548),
(3, 'APPLICATIONS', 0, 1, 1, 1526118548, 1526118548),
(4, 'VERIFICATION', 0, 1, 1, 1526118548, 1526118548),
(5, 'GROUP_FORMATION', 0, 1, 1, 1526118548, 1526118548),
(6, 'LAC', 0, 1, 1, 1526118548, 1526118548),
(7, 'FUNDS', 0, 1, 1, 1526118548, 1526118548),
(8, 'TAKAFUL', 0, 1, 1, 1526118548, 1526118548),
(9, 'DISBURSEMENT', 0, 1, 1, 1526118548, 1526118548),
(10, 'SEARCH_LOAN', 0, 1, 1, 1526118548, 1526118548),
(11, 'RECOVERIES', 0, 1, 1, 1526118548, 1526118548),
(12, 'VIEW_MEMBERS', 0, 1, 1, 1526118548, 1526118548),
(13, 'MANAGE_MEMBERS', 0, 1, 1, 1526118548, 1526118548),
(14, 'VIEW_APPLICATIONS', 0, 1, 1, 1526118548, 1526118548),
(15, 'MANAGE_APPLICATIONS', 0, 1, 1, 1526118548, 1526118548),
(16, 'MANAGE_FUNDS', 0, 1, 1, 1526118548, 1526118548),
(17, 'APPROVE_FUNDS', 0, 1, 1, 1526118548, 1526118548);

--
-- Dumping data for table `versions`
--

INSERT INTO `versions` (`id`, `version_no`, `type`, `assigned_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted`) VALUES
(1, 6, 'db', 2, 2, 1, 1526121377, 1540642608, 0),
(2, 2, 'member_form', 2, 2, 1, 1526121377, 1514887344, 0),
(3, 2, 'member_list', 2, 2, 1, 1526121377, 1540296826, 0),
(4, 1, 'application_form', 2, 2, 1, 1526121377, 1540892138, 0),
(5, 2, 'application_list', 2, 2, 1, 1526121377, 1540892130, 0),
(6, 3, 'social_appraisal_list', 2, 2, 1, 1526121377, 1514887344, 0),
(7, 2, 'business_appraisal_list', 2, 2, 1, 1526121377, 1514887344, 0),
(8, 2, 'ba_dropdowns_list', 2, 2, 1, 1526121377, 1540299064, 0),
(9, 1, 'branch_list', 2, 2, 1, 1526121377, 1540894962, 0),
(10, 9, 'filter_list', 2, 2, 1, 1526121377, 1540894971, 0),
(11, 5, 'App Version', 1, 1, 1, 1539843763, 1539844107, 0),
(12, 1, 'appraisals', 1, 1, 1, 1539843763, 1539844107, 0),
(13, 5, 'documents_data', 1, 1, 1, 1539843763, 1539844107, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
