-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2019 at 09:59 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `access_tokens`
--

CREATE TABLE `access_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) NOT NULL,
  `auth_code` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `acc_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `bank_info` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `funding_line` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `dt_opening` int(11) DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounts_test`
--

CREATE TABLE `accounts_test` (
  `id` int(11) NOT NULL,
  `acc_no` varchar(30) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `bank_info` varchar(100) NOT NULL,
  `dt_opening` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `funding_line` varchar(20) NOT NULL,
  `purpose` enum('disbr','recov','donat','ops') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(11) NOT NULL,
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `id` int(11) NOT NULL,
  `module` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `module_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities_logs`
--

CREATE TABLE `activities_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `parent_type` varchar(50) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `full_address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(50) NOT NULL,
  `country` varchar(100) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `address_logs`
--

CREATE TABLE `address_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `address` text,
  `address_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aging_reports`
--

CREATE TABLE `aging_reports` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `start_month` date NOT NULL,
  `one_month` double DEFAULT '0',
  `next_three_months` double NOT NULL DEFAULT '0',
  `next_six_months` double NOT NULL DEFAULT '0',
  `next_one_year` double NOT NULL DEFAULT '0',
  `next_two_year` double NOT NULL DEFAULT '0',
  `next_three_year` double NOT NULL DEFAULT '0',
  `next_five_year` double NOT NULL DEFAULT '0',
  `total` double NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `agriculture_logs`
--

CREATE TABLE `agriculture_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `user_id` smallint(6) NOT NULL,
  `api` varchar(100) NOT NULL,
  `count` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `fee` decimal(19,0) DEFAULT '0',
  `application_no` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(5) NOT NULL,
  `project_table` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activity_id` int(5) NOT NULL DEFAULT '0',
  `product_id` int(5) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) DEFAULT NULL,
  `field_id` int(5) NOT NULL,
  `field_area_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `no_of_times` tinyint(3) NOT NULL,
  `bzns_cond` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `who_will_work` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name_of_other` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_cnic` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `req_amount` decimal(19,4) NOT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `is_biometric` tinyint(4) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `is_urban` tinyint(3) NOT NULL,
  `reject_type` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reject_reason` text COLLATE utf8_unicode_ci,
  `comments` text COLLATE utf8_unicode_ci,
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `dt_applied_old` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications_logs`
--

CREATE TABLE `applications_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_actions`
--

CREATE TABLE `application_actions` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `pre_action` int(11) NOT NULL DEFAULT '0',
  `expiry_date` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `application_logs`
--

CREATE TABLE `application_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `application_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appraisals`
--

CREATE TABLE `appraisals` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `appraisal_table` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appraisals_business`
--

CREATE TABLE `appraisals_business` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `place_of_business` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `fixed_business_assets` text COLLATE utf8_unicode_ci,
  `fixed_business_assets_amount` decimal(19,0) DEFAULT NULL,
  `running_capital` text COLLATE utf8_unicode_ci,
  `running_capital_amount` decimal(19,0) DEFAULT NULL,
  `business_expenses` text COLLATE utf8_unicode_ci,
  `business_expenses_amount` decimal(19,0) DEFAULT NULL,
  `new_required_assets` text COLLATE utf8_unicode_ci NOT NULL,
  `new_required_assets_amount` decimal(19,0) NOT NULL,
  `business_appraisal_address` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `bm_verify_latitude` float NOT NULL DEFAULT '0',
  `bm_verify_longitude` float NOT NULL DEFAULT '0',
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approved_on` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appraisals_business_logs`
--

CREATE TABLE `appraisals_business_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appraisals_social`
--

CREATE TABLE `appraisals_social` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `poverty_index` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `house_ownership` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `house_rent_amount` decimal(19,0) DEFAULT NULL,
  `land_size` int(11) NOT NULL,
  `total_family_members` int(11) NOT NULL,
  `no_of_earning_hands` int(11) NOT NULL,
  `ladies` int(11) NOT NULL,
  `gents` int(11) NOT NULL,
  `source_of_income` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `total_household_income` decimal(19,0) NOT NULL,
  `utility_bills` decimal(19,4) NOT NULL,
  `educational_expenses` decimal(19,4) NOT NULL,
  `medical_expenses` decimal(19,4) NOT NULL,
  `kitchen_expenses` decimal(19,4) NOT NULL,
  `monthly_savings` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(19,0) DEFAULT NULL,
  `date_of_maturity` int(11) DEFAULT NULL,
  `other_expenses` decimal(19,4) NOT NULL,
  `total_expenses` decimal(19,0) DEFAULT NULL,
  `other_loan` tinyint(4) NOT NULL,
  `loan_amount` decimal(19,0) DEFAULT NULL,
  `economic_dealings` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `social_behaviour` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fatal_disease` tinyint(4) NOT NULL DEFAULT '0',
  `business_income` decimal(19,0) NOT NULL DEFAULT '0',
  `job_income` decimal(19,0) NOT NULL DEFAULT '0',
  `house_rent_income` decimal(19,0) NOT NULL DEFAULT '0',
  `other_income` decimal(19,0) NOT NULL DEFAULT '0',
  `expected_increase_in_income` decimal(19,0) DEFAULT NULL,
  `social_appraisal_address` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `description_image` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `bm_verify_latitude` float NOT NULL DEFAULT '0',
  `bm_verify_longitude` float NOT NULL DEFAULT '0',
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approved_on` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appraisals_social_logs`
--

CREATE TABLE `appraisals_social_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_reports`
--

CREATE TABLE `archive_reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `region_id` int(11) DEFAULT '0',
  `area_id` int(11) DEFAULT '0',
  `branch_id` int(11) DEFAULT '0',
  `team_id` int(11) DEFAULT '0',
  `field_id` int(11) DEFAULT '0',
  `project_id` int(11) DEFAULT '0',
  `activity_id` int(11) DEFAULT '0',
  `product_id` int(11) DEFAULT '0',
  `date_filter` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `file_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `requested_by` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `do_delete` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8_unicode_ci,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opening_date` int(11) DEFAULT '0',
  `full_address` text COLLATE utf8_unicode_ci,
  `region_id` int(11) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `opening_date_old` date DEFAULT NULL,
  `created_on_old` datetime DEFAULT NULL,
  `updated_on_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `areas_logs`
--

CREATE TABLE `areas_logs` (
  `id` int(11) NOT NULL,
  `field` varchar(100) NOT NULL,
  `old_value` varchar(100) NOT NULL,
  `new_value` varchar(100) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `audit_data`
--

CREATE TABLE `audit_data` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `data` blob,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `audit_entry`
--

CREATE TABLE `audit_entry` (
  `id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int(11) DEFAULT '0',
  `duration` float DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `request_method` varchar(16) DEFAULT NULL,
  `ajax` int(1) NOT NULL DEFAULT '0',
  `route` varchar(255) DEFAULT NULL,
  `memory_max` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `authorization_codes`
--

CREATE TABLE `authorization_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment_`
--

CREATE TABLE `auth_assignment_` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_`
--

CREATE TABLE `auth_item_` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child_`
--

CREATE TABLE `auth_item_child_` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule_`
--

CREATE TABLE `auth_rule_` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `awp`
--

CREATE TABLE `awp` (
  `id` int(11) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `region_id` int(5) NOT NULL,
  `project_id` int(5) NOT NULL DEFAULT '0',
  `month` varchar(15) NOT NULL,
  `no_of_loans` int(11) NOT NULL DEFAULT '0',
  `amount_disbursed` int(11) NOT NULL DEFAULT '0',
  `active_loans` int(11) NOT NULL DEFAULT '0',
  `monthly_olp` int(11) NOT NULL DEFAULT '0',
  `avg_loan_size` int(11) NOT NULL DEFAULT '0',
  `monthly_closed_loans` int(11) NOT NULL DEFAULT '0',
  `monthly_recovery` int(11) NOT NULL DEFAULT '0',
  `avg_recovery` int(11) NOT NULL DEFAULT '0',
  `funds_required` int(11) NOT NULL DEFAULT '0',
  `actual_recovery` int(11) NOT NULL DEFAULT '0',
  `actual_disbursement` int(11) NOT NULL DEFAULT '0',
  `actual_no_of_loans` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `is_lock` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awp_branch_sustainability`
--

CREATE TABLE `awp_branch_sustainability` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `branch_code` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `month` varchar(150) NOT NULL DEFAULT '0',
  `amount_disbursed` int(11) NOT NULL DEFAULT '0',
  `percentage` double NOT NULL DEFAULT '5',
  `income` double NOT NULL DEFAULT '0',
  `actual_expense` double NOT NULL DEFAULT '0',
  `surplus_deficit` double NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awp_loan_management_cost`
--

CREATE TABLE `awp_loan_management_cost` (
  `id` int(11) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `region_id` int(5) NOT NULL,
  `date_of_opening` date NOT NULL,
  `opening_active_loans` int(11) NOT NULL,
  `closing_active_loans` int(11) NOT NULL,
  `average` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `lmc` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awp_overdue`
--

CREATE TABLE `awp_overdue` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `month` varchar(15) DEFAULT NULL,
  `date_of_opening` date DEFAULT NULL,
  `overdue_numbers` int(11) DEFAULT NULL,
  `overdue_amount` int(11) DEFAULT NULL,
  `awp_active_loans` int(11) DEFAULT NULL,
  `awp_olp` int(11) DEFAULT NULL,
  `active_loans` int(11) DEFAULT NULL,
  `olp` int(11) DEFAULT NULL,
  `diff_active_loans` int(11) DEFAULT NULL,
  `diff_olp` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awp_project_mapping`
--

CREATE TABLE `awp_project_mapping` (
  `id` int(11) NOT NULL,
  `awp_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `no_of_loans` int(11) NOT NULL DEFAULT '0',
  `avg_loan_size` int(11) NOT NULL DEFAULT '0',
  `disbursement_amount` int(11) NOT NULL DEFAULT '0',
  `monthly_olp` int(11) NOT NULL DEFAULT '0',
  `active_loans` int(11) NOT NULL DEFAULT '0',
  `monthly_closed_loans` int(11) NOT NULL DEFAULT '0',
  `monthly_recovery` int(11) NOT NULL DEFAULT '0',
  `avg_recovery` int(11) NOT NULL DEFAULT '0',
  `funds_required` int(11) NOT NULL DEFAULT '0',
  `actual_recovery` int(11) NOT NULL DEFAULT '0',
  `actual_disbursement` int(11) NOT NULL DEFAULT '0',
  `actual_no_of_loans` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awp_target_vs_achievement`
--

CREATE TABLE `awp_target_vs_achievement` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `month` varchar(15) NOT NULL,
  `target_loans` int(11) NOT NULL DEFAULT '0',
  `target_amount` int(11) NOT NULL DEFAULT '0',
  `achieved_loans` int(11) NOT NULL DEFAULT '0',
  `achieved_amount` int(11) NOT NULL DEFAULT '0',
  `loans_dif` int(11) NOT NULL DEFAULT '0',
  `amount_dif` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `branch_detail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `branch_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_assets`
--

CREATE TABLE `ba_assets` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `assets_list` text COLLATE utf8_unicode_ci NOT NULL,
  `total_amount` decimal(19,0) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_business_expenses`
--

CREATE TABLE `ba_business_expenses` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `expense_title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `expenses_value` decimal(19,4) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_details`
--

CREATE TABLE `ba_details` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `business_income` decimal(19,0) NOT NULL DEFAULT '0',
  `job_income` decimal(19,0) NOT NULL DEFAULT '0',
  `house_rent_income` decimal(19,0) NOT NULL DEFAULT '0',
  `other_income` decimal(19,0) NOT NULL DEFAULT '0',
  `total_income` decimal(19,0) NOT NULL,
  `expected_increase_in_income` decimal(19,0) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `description_image` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_fixed_business_assets`
--

CREATE TABLE `ba_fixed_business_assets` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `assets` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `existing_price` decimal(19,4) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_new_required_assets`
--

CREATE TABLE `ba_new_required_assets` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `assets` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchasing_price` decimal(19,4) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ba_running_capital`
--

CREATE TABLE `ba_running_capital` (
  `id` int(11) NOT NULL,
  `ba_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `assets` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchasing_price` decimal(19,4) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `cnic` varchar(20) NOT NULL,
  `reason` text NOT NULL,
  `description` text,
  `location` varchar(100) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `borrowers_logs`
--

CREATE TABLE `borrowers_logs` (
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `cnic` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uc` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `village` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city_id` int(11) NOT NULL,
  `tehsil_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `province_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `opening_date` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT NULL,
  `cr_division_id` int(11) DEFAULT NULL,
  `effective_date` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `opening_date_old` date DEFAULT NULL,
  `created_on_old` datetime DEFAULT NULL,
  `updated_on_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches_logs`
--

CREATE TABLE `branches_logs` (
  `id` int(20) NOT NULL,
  `source_id` int(20) NOT NULL,
  `target_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_account_mapping`
--

CREATE TABLE `branch_account_mapping` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_projects_mapping`
--

CREATE TABLE `branch_projects_mapping` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_projects_mapping_`
--

CREATE TABLE `branch_projects_mapping_` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_requests`
--

CREATE TABLE `branch_requests` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `uc` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `village` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city_id` int(11) NOT NULL,
  `tehsil_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `projects` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `opening_date` int(11) DEFAULT '0',
  `effective_date` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `reject_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cr_division_id` int(11) NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_request_actions`
--

CREATE TABLE `branch_request_actions` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `remarks` text,
  `pre_action` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `business_appraisal`
--

CREATE TABLE `business_appraisal` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `business_type` tinyint(1) NOT NULL,
  `business` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `place_of_business` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `business_appraisal_address` text COLLATE utf8_unicode_ci,
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `bm_verify_latitude` float NOT NULL DEFAULT '0',
  `bm_verify_longitude` float NOT NULL DEFAULT '0',
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approved_on` int(11) DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cih_transactions_mapping`
--

CREATE TABLE `cih_transactions_mapping` (
  `id` int(11) NOT NULL,
  `cih_type_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `province_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `city_logs`
--

CREATE TABLE `city_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config_rules`
--

CREATE TABLE `config_rules` (
  `id` int(11) NOT NULL,
  `group` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `priority` smallint(6) NOT NULL,
  `key` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `operator` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `operator_desc` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `parent_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `continent` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_logs`
--

CREATE TABLE `country_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `credit_divisions`
--

CREATE TABLE `credit_divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_on_old` datetime DEFAULT NULL,
  `updated_on_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cr_divisions_logs`
--

CREATE TABLE `cr_divisions_logs` (
  `id` int(20) NOT NULL,
  `source_id` int(20) NOT NULL,
  `target_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` smallint(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desig_label` varchar(100) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `sorting` tinyint(4) DEFAULT '0',
  `network` tinyint(4) NOT NULL DEFAULT '0',
  `progress_report` tinyint(4) NOT NULL DEFAULT '0',
  `projects` tinyint(4) NOT NULL DEFAULT '0',
  `districts` tinyint(4) NOT NULL DEFAULT '0',
  `products` tinyint(4) NOT NULL DEFAULT '0',
  `analysis` tinyint(4) NOT NULL DEFAULT '0',
  `search_loan` tinyint(4) NOT NULL DEFAULT '0',
  `news` tinyint(4) NOT NULL DEFAULT '0',
  `maps` tinyint(4) NOT NULL DEFAULT '0',
  `staff` tinyint(4) NOT NULL DEFAULT '0',
  `links` tinyint(4) NOT NULL DEFAULT '0',
  `filters` tinyint(4) NOT NULL DEFAULT '0',
  `mobile` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `uu_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imei_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `os_version` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `device_model` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `push_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disable_logs`
--

CREATE TABLE `disable_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `disbursements`
--

CREATE TABLE `disbursements` (
  `id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `date_disbursed` int(11) NOT NULL DEFAULT '0',
  `venue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `date_disburse_old` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursement_logs`
--

CREATE TABLE `disbursement_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `district_logs`
--

CREATE TABLE `district_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `province_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `division_logs`
--

CREATE TABLE `division_logs` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `module_type` varchar(30) NOT NULL,
  `module_id` int(11) NOT NULL DEFAULT '0',
  `parent_type` varchar(30) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) NOT NULL,
  `field_id` int(5) NOT NULL,
  `project_id` int(5) NOT NULL,
  `amount` decimal(8,0) NOT NULL,
  `receive_date` int(11) NOT NULL,
  `receipt_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `recv_date_old` date DEFAULT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations_logs`
--

CREATE TABLE `donations_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails_list`
--

CREATE TABLE `emails_list` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emails_list_details`
--

CREATE TABLE `emails_list_details` (
  `id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  `receiver_email` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `receiver_email` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `name` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `team_id` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` tinyint(3) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `field_areas`
--

CREATE TABLE `field_areas` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `team_id` int(11) NOT NULL,
  `latitude` float NOT NULL DEFAULT '0',
  `longitude` float NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_requests`
--

CREATE TABLE `fund_requests` (
  `id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(11) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `requested_amount` decimal(19,0) NOT NULL,
  `approved_amount` decimal(19,0) NOT NULL DEFAULT '0',
  `total_loans` int(11) NOT NULL,
  `status` varchar(15) NOT NULL,
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approved_on` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fund_requests_details`
--

CREATE TABLE `fund_requests_details` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `fund_request_id` int(11) NOT NULL,
  `total_loans` int(11) NOT NULL,
  `total_requested_amount` decimal(19,0) NOT NULL,
  `total_approved_amount` decimal(19,0) NOT NULL DEFAULT '0',
  `cheque_no` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `is_locked` tinyint(3) NOT NULL,
  `br_serial` int(11) NOT NULL,
  `grp_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `group_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grp_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `group_size` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `reject_reason` text COLLATE utf8_unicode_ci,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `dt_entry_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_actions`
--

CREATE TABLE `group_actions` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `pre_action` int(11) NOT NULL DEFAULT '0',
  `expiry_date` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guarantors`
--

CREATE TABLE `guarantors` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `parentage` varchar(50) NOT NULL,
  `cnic` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `parent_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE `lists` (
  `id` int(11) NOT NULL,
  `list_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` tinyint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lists_`
--

CREATE TABLE `lists_` (
  `id` int(11) NOT NULL,
  `list_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` tinyint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `fund_request_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(5) DEFAULT NULL,
  `project_table` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_approved` int(11) DEFAULT '0',
  `loan_amount` decimal(19,0) NOT NULL,
  `cheque_no` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inst_amnt` decimal(19,0) NOT NULL,
  `inst_months` decimal(19,4) NOT NULL,
  `inst_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `date_disbursed` int(11) DEFAULT '0',
  `cheque_dt` int(11) DEFAULT '0',
  `disbursement_id` int(11) NOT NULL DEFAULT '0',
  `activity_id` int(5) NOT NULL DEFAULT '0',
  `product_id` int(5) NOT NULL,
  `group_id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) NOT NULL,
  `field_id` int(5) NOT NULL,
  `loan_expiry` int(11) DEFAULT NULL,
  `loan_completed_date` int(11) DEFAULT '0',
  `old_sanc_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `br_serial` int(11) DEFAULT NULL,
  `sanction_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `due` decimal(8,0) NOT NULL DEFAULT '0',
  `overdue` decimal(8,0) NOT NULL DEFAULT '0',
  `balance` decimal(8,0) NOT NULL DEFAULT '0',
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `reject_reason` text COLLATE utf8_unicode_ci,
  `attendance_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'info_not_available',
  `is_lock` tinyint(3) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `date_approved_old` date DEFAULT NULL,
  `date_disburse_old` date DEFAULT NULL,
  `cheque_dt_old` date DEFAULT NULL,
  `loan_expiry_old` date DEFAULT NULL,
  `loan_completed_dt_old` date DEFAULT NULL,
  `dt_entry_old` timestamp NULL DEFAULT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans_logs`
--

CREATE TABLE `loans_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_actions`
--

CREATE TABLE `loan_actions` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `pre_action` int(11) NOT NULL DEFAULT '0',
  `expiry_date` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loan_tranches`
--

CREATE TABLE `loan_tranches` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `tranch_no` int(11) NOT NULL,
  `tranch_amount` decimal(19,0) NOT NULL,
  `date_disbursed` int(11) NOT NULL DEFAULT '0',
  `disbursement_id` int(11) NOT NULL DEFAULT '0',
  `cheque_no` varchar(100) DEFAULT NULL,
  `fund_request_id` int(11) NOT NULL DEFAULT '0',
  `tranch_date` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loan_tranches_actions`
--

CREATE TABLE `loan_tranches_actions` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `pre_action` int(11) NOT NULL DEFAULT '0',
  `expiry_date` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) DEFAULT NULL,
  `field_id` int(5) DEFAULT NULL,
  `full_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `parentage` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `parentage_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cnic` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `dob` int(11) NOT NULL DEFAULT '0',
  `education` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `marital_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `family_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `family_member_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `family_member_cnic` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `family_member_left_thumb` text COLLATE utf8_unicode_ci,
  `family_member_right_thumb` text COLLATE utf8_unicode_ci,
  `referral_id` int(11) DEFAULT NULL,
  `religion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `is_disable` tinyint(4) NOT NULL,
  `disability_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `left_index` text COLLATE utf8_unicode_ci,
  `right_index` text COLLATE utf8_unicode_ci,
  `left_thumb` text COLLATE utf8_unicode_ci,
  `right_thumb` text COLLATE utf8_unicode_ci,
  `profile_pic` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `dob_old` date DEFAULT NULL,
  `dt_entry_old` timestamp NULL DEFAULT NULL,
  `team_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_address`
--

CREATE TABLE `members_address` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_current` tinyint(3) DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_entry_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_email`
--

CREATE TABLE `members_email` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_current` tinyint(3) DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_entry_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_logs`
--

CREATE TABLE `members_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_phone`
--

CREATE TABLE `members_phone` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `phone_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_current` tinyint(3) DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_entry_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_permissions`
--

CREATE TABLE `mobile_permissions` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `mobile_screen_id` smallint(6) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` smallint(6) NOT NULL,
  `updated_by` smallint(6) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_screens`
--

CREATE TABLE `mobile_screens` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `created_by` smallint(6) NOT NULL,
  `updated_by` smallint(6) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_description` text COLLATE utf8_unicode_ci NOT NULL,
  `news_date` int(11) DEFAULT '0',
  `image_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `parent_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `operations`
--

CREATE TABLE `operations` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `operation_type_id` int(11) NOT NULL,
  `credit` decimal(10,0) NOT NULL,
  `receipt_no` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_date` int(11) NOT NULL DEFAULT '0',
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) NOT NULL,
  `field_id` int(5) NOT NULL,
  `project_id` int(5) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `recv_date_old` date DEFAULT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operations_logs`
--

CREATE TABLE `operations_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operation_type`
--

CREATE TABLE `operation_type` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(20) DEFAULT NULL,
  `description` text,
  `status` int(2) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `inst_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_activity_mapping`
--

CREATE TABLE `product_activity_mapping` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_activity_mapping_`
--

CREATE TABLE `product_activity_mapping_` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_reports`
--

CREATE TABLE `progress_reports` (
  `id` int(11) NOT NULL,
  `report_date` int(11) DEFAULT '0',
  `project_id` int(11) NOT NULL,
  `gender` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `period` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `comments` text COLLATE utf8_unicode_ci,
  `status` tinyint(3) DEFAULT NULL,
  `is_verified` tinyint(3) DEFAULT NULL,
  `do_update` int(11) DEFAULT NULL,
  `do_delete` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_report_details`
--

CREATE TABLE `progress_report_details` (
  `id` int(11) NOT NULL,
  `progress_report_id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `branch_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `no_of_loans` int(11) NOT NULL,
  `family_loans` int(11) NOT NULL,
  `female_loans` int(11) NOT NULL,
  `active_loans` int(11) NOT NULL,
  `cum_disb` bigint(20) NOT NULL,
  `cum_due` bigint(20) NOT NULL,
  `cum_recv` bigint(20) NOT NULL,
  `overdue_borrowers` int(11) NOT NULL,
  `overdue_amount` bigint(20) NOT NULL,
  `overdue_percentage` decimal(5,0) NOT NULL,
  `par_amount` bigint(20) NOT NULL,
  `par_percentage` decimal(5,0) NOT NULL,
  `not_yet_due` bigint(20) NOT NULL,
  `olp_amount` bigint(20) NOT NULL,
  `recovery_percentage` decimal(7,4) DEFAULT NULL,
  `cih` bigint(20) NOT NULL,
  `mdp` bigint(20) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_report_update`
--

CREATE TABLE `progress_report_update` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL DEFAULT '0',
  `area_id` int(5) NOT NULL DEFAULT '0',
  `branch_id` int(5) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_table` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `donor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `funding_line` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `started_date` int(11) DEFAULT '0',
  `logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `loan_amount_limit` int(11) NOT NULL DEFAULT '50000',
  `description` text COLLATE utf8_unicode_ci,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_agriculture`
--

CREATE TABLE `projects_agriculture` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `owner` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `land_area_size` int(11) DEFAULT NULL,
  `land_area_type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `village_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uc_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uc_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `crop_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `crops` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_disabled`
--

CREATE TABLE `projects_disabled` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `is_khidmat_card_holder` tinyint(4) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `disability` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nature` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `physical_disability` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visual_disability` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `communicative_disability` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `disabilities_instruments` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_applied_old` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects_tevta`
--

CREATE TABLE `projects_tevta` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `institute_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_of_diploma` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration_of_diploma` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbte_or_ttb` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registration_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roll_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_applied_old` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_appraisals_mapping`
--

CREATE TABLE `project_appraisals_mapping` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `appraisal_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_product_mapping`
--

CREATE TABLE `project_product_mapping` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recoveries`
--

CREATE TABLE `recoveries` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) NOT NULL,
  `field_id` int(5) NOT NULL,
  `due_date` int(11) DEFAULT '0',
  `receive_date` int(11) NOT NULL,
  `amount` decimal(8,0) NOT NULL,
  `receipt_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(5) NOT NULL,
  `type` tinyint(3) NOT NULL,
  `source` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_locked` tinyint(3) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `recv_date_old` date DEFAULT NULL,
  `due_date_old` date DEFAULT NULL,
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `platform` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recoveries_logs`
--

CREATE TABLE `recoveries_logs` (
  `id` int(11) NOT NULL,
  `old_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` int(11) NOT NULL,
  `user_id` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recovery_errors`
--

CREATE TABLE `recovery_errors` (
  `id` int(11) NOT NULL,
  `recovery_files_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL DEFAULT '0',
  `region_id` int(11) NOT NULL DEFAULT '0',
  `bank_branch_name` varchar(50) DEFAULT NULL,
  `bank_branch_code` varchar(50) DEFAULT NULL,
  `source` enum('branch','bi','hbl','hble','mcb','nbp','ep','jc','omni') DEFAULT 'branch',
  `sanction_no` varchar(50) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `recv_date` int(11) NOT NULL,
  `credit` decimal(8,0) NOT NULL DEFAULT '0',
  `receipt_no` varchar(20) NOT NULL,
  `balance` decimal(8,0) NOT NULL DEFAULT '0',
  `error_description` varchar(255) NOT NULL,
  `comments` text,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `status` enum('0','1','2') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recovery_files`
--

CREATE TABLE `recovery_files` (
  `id` int(11) NOT NULL,
  `source` enum('branch','bi','hbl','hble','mcb','nbp','ep','jc','omni','WROFF') DEFAULT 'branch',
  `description` varchar(255) NOT NULL,
  `file_date` int(11) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `status` enum('0','1','2') DEFAULT NULL,
  `total_records` int(11) DEFAULT NULL,
  `inserted_records` int(11) DEFAULT NULL,
  `error_records` int(11) DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `cr_division_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8_unicode_ci,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opening_date` int(11) DEFAULT '0',
  `full_address` text COLLATE utf8_unicode_ci,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `opening_date_old` date DEFAULT NULL,
  `created_on_old` datetime NOT NULL,
  `updated_on_old` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `right_off`
--

CREATE TABLE `right_off` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `reason` text NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL DEFAULT '0',
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `due_date` int(11) NOT NULL DEFAULT '0',
  `schdl_amnt` decimal(10,0) NOT NULL DEFAULT '0',
  `overdue` decimal(8,0) NOT NULL DEFAULT '0',
  `overdue_log` decimal(8,0) NOT NULL DEFAULT '0',
  `advance` decimal(8,0) NOT NULL DEFAULT '0',
  `advance_log` decimal(8,0) NOT NULL DEFAULT '0',
  `due_amnt` decimal(8,0) NOT NULL DEFAULT '0',
  `credit` decimal(8,0) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` int(11) DEFAULT NULL,
  `platform` tinyint(4) NOT NULL DEFAULT '1',
  `recv_date_old` date DEFAULT NULL,
  `updated_on_old` timestamp NULL DEFAULT NULL,
  `due_date_old` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_fields_configs`
--

CREATE TABLE `section_fields_configs` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `key_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `parent_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_fields_configs_`
--

CREATE TABLE `section_fields_configs_` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `key_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `parent_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `sms_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `number` decimal(19,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_appraisal`
--

CREATE TABLE `social_appraisal` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `poverty_index` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `house_ownership` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `house_rent_amount` decimal(19,0) NOT NULL DEFAULT '0',
  `land_size` int(11) NOT NULL,
  `total_family_members` int(11) NOT NULL,
  `no_of_earning_hands` int(11) NOT NULL,
  `ladies` int(11) NOT NULL,
  `gents` int(11) NOT NULL,
  `source_of_income` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `total_household_income` decimal(19,0) NOT NULL,
  `utility_bills` decimal(19,4) NOT NULL,
  `educational_expenses` decimal(19,4) NOT NULL,
  `medical_expenses` decimal(19,4) NOT NULL,
  `kitchen_expenses` decimal(19,4) NOT NULL,
  `monthly_savings` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(19,0) DEFAULT NULL,
  `date_of_maturity` int(11) NOT NULL DEFAULT '0',
  `other_expenses` decimal(19,4) NOT NULL,
  `total_expenses` decimal(19,4) NOT NULL,
  `other_loan` tinyint(4) NOT NULL,
  `loan_amount` decimal(19,0) NOT NULL DEFAULT '0',
  `economic_dealings` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `social_behaviour` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fatal_disease` tinyint(4) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `description_image` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `social_appraisal_address` text COLLATE utf8_unicode_ci,
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `bm_verify_latitude` float NOT NULL DEFAULT '0',
  `bm_verify_longitude` float NOT NULL DEFAULT '0',
  `is_lock` tinyint(3) NOT NULL DEFAULT '0',
  `approved_by` int(11) DEFAULT '0',
  `approved_on` int(11) DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_appraisal_diseases_mapping`
--

CREATE TABLE `social_appraisal_diseases_mapping` (
  `id` int(11) NOT NULL,
  `social_appraisal_id` int(11) NOT NULL,
  `disease_id` int(11) NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `structure_transfer`
--

CREATE TABLE `structure_transfer` (
  `id` int(11) NOT NULL,
  `obj_type` varchar(20) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `old_value` int(11) NOT NULL,
  `new_value` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` smallint(6) NOT NULL,
  `updated_by` smallint(6) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `branch_id` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` tinyint(3) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tehsils`
--

CREATE TABLE `tehsils` (
  `id` smallint(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `region_id` int(5) NOT NULL,
  `area_id` int(5) NOT NULL,
  `branch_id` int(5) NOT NULL,
  `team_id` int(5) NOT NULL,
  `field_id` int(5) NOT NULL,
  `amount` decimal(19,4) NOT NULL,
  `tax` decimal(19,4) NOT NULL,
  `account_id` int(11) NOT NULL,
  `deposit_slip_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deposit_date` int(11) DEFAULT '0',
  `deposited_by` int(11) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `father_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cnic` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `address` text COLLATE utf8_unicode_ci,
  `alternate_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login_at` int(11) DEFAULT '0',
  `last_login_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL DEFAULT '0',
  `longitude` float NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joining_date` int(11) DEFAULT '0',
  `emp_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation_id` int(11) NOT NULL DEFAULT '0',
  `is_block` tinyint(3) NOT NULL DEFAULT '0',
  `reason` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `block_date` int(11) DEFAULT '0',
  `team_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  `term_and_condition` tinyint(3) DEFAULT '0',
  `do_reset_password` tinyint(4) NOT NULL DEFAULT '0',
  `do_complete_profile` tinyint(4) NOT NULL DEFAULT '0',
  `left_thumb_impression` text COLLATE utf8_unicode_ci,
  `right_thumb_impression` text COLLATE utf8_unicode_ci,
  `post_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` int(11) NOT NULL DEFAULT '0',
  `expires_at` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `joining_date_old` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_old` datetime DEFAULT NULL,
  `modified_old` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_`
--

CREATE TABLE `users_` (
  `id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `father_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cnic` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `address` text COLLATE utf8_unicode_ci,
  `alternate_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login_at` int(11) DEFAULT '0',
  `last_login_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL DEFAULT '0',
  `longitude` float NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joining_date` int(11) DEFAULT '0',
  `emp_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation_id` int(11) NOT NULL DEFAULT '0',
  `is_block` tinyint(3) NOT NULL DEFAULT '0',
  `reason` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `block_date` int(11) DEFAULT '0',
  `team_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  `term_and_condition` tinyint(3) DEFAULT '0',
  `do_reset_password` tinyint(4) NOT NULL DEFAULT '0',
  `do_complete_profile` tinyint(4) NOT NULL DEFAULT '0',
  `left_thumb_impression` text COLLATE utf8_unicode_ci,
  `right_thumb_impression` text COLLATE utf8_unicode_ci,
  `post_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` int(11) NOT NULL DEFAULT '0',
  `expires_at` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `joining_date_old` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_old` datetime DEFAULT NULL,
  `modified_old` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_migrated`
--

CREATE TABLE `users_migrated` (
  `id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `father_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cnic` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `address` text COLLATE utf8_unicode_ci,
  `alternate_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login_at` int(11) DEFAULT '0',
  `last_login_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` float NOT NULL DEFAULT '0',
  `longitude` float NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joining_date` int(11) DEFAULT '0',
  `emp_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation_id` int(11) NOT NULL DEFAULT '0',
  `is_block` tinyint(3) NOT NULL DEFAULT '0',
  `reason` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `block_date` int(11) DEFAULT '0',
  `team_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  `term_and_condition` tinyint(3) DEFAULT '0',
  `do_reset_password` tinyint(4) NOT NULL DEFAULT '0',
  `do_complete_profile` tinyint(4) NOT NULL DEFAULT '0',
  `thumb_impression` text COLLATE utf8_unicode_ci,
  `post_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` int(11) NOT NULL DEFAULT '0',
  `expires_at` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `joining_date_old` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_old` datetime DEFAULT NULL,
  `modified_old` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_devices_`
--

CREATE TABLE `user_devices_` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_hierarchy_change_request`
--

CREATE TABLE `user_hierarchy_change_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL DEFAULT '0',
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `field_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `recommended_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_projects_mapping`
--

CREATE TABLE `user_projects_mapping` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_projects_mapping_`
--

CREATE TABLE `user_projects_mapping_` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_structure_mapping`
--

CREATE TABLE `user_structure_mapping` (
  `user_id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `obj_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_structure_mapping_`
--

CREATE TABLE `user_structure_mapping_` (
  `user_id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `obj_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `skip_reason` text,
  `longitude` float NOT NULL DEFAULT '0',
  `latitude` float NOT NULL DEFAULT '0',
  `verified_at` int(11) NOT NULL DEFAULT '0',
  `thumb_impression` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `versions`
--

CREATE TABLE `versions` (
  `id` int(11) NOT NULL,
  `version_no` int(11) NOT NULL,
  `type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_sections`
--

CREATE TABLE `view_sections` (
  `id` int(11) NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `section_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `section_description` text COLLATE utf8_unicode_ci NOT NULL,
  `section_table_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_sections_`
--

CREATE TABLE `view_sections_` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `section_description` text COLLATE utf8_unicode_ci NOT NULL,
  `section_table_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_section_fields`
--

CREATE TABLE `view_section_fields` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `table_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_section_fields_`
--

CREATE TABLE `view_section_fields_` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `table_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_tokens`
--
ALTER TABLE `access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id_index_accounts` (`branch_id`),
  ADD KEY `created_by_index_accounts` (`created_by`);

--
-- Indexes for table `accounts_test`
--
ALTER TABLE `accounts_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_products_activities` (`product_id`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`city`);

--
-- Indexes for table `aging_reports`
--
ALTER TABLE `aging_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id_index_applications` (`member_id`),
  ADD KEY `req_amount_index_applications` (`req_amount`),
  ADD KEY `FK_projects_applications` (`project_id`),
  ADD KEY `FK_activities_applications` (`activity_id`),
  ADD KEY `FK_products_applications` (`product_id`),
  ADD KEY `FK_regions_applications` (`region_id`),
  ADD KEY `FK_areas_applications` (`area_id`),
  ADD KEY `FK_branches_applications` (`branch_id`),
  ADD KEY `deleted_index_applications` (`deleted`),
  ADD KEY `status` (`status`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `deleted` (`deleted`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `applications_logs`
--
ALTER TABLE `applications_logs`
  ADD KEY `FK_applications_applications_logs` (`id`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `application_actions`
--
ALTER TABLE `application_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action` (`action`),
  ADD KEY `status` (`status`),
  ADD KEY `expiry_date` (`expiry_date`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action_2` (`action`),
  ADD KEY `status_2` (`status`);

--
-- Indexes for table `appraisals`
--
ALTER TABLE `appraisals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appraisals_business`
--
ALTER TABLE `appraisals_business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_ba` (`application_id`);

--
-- Indexes for table `appraisals_business_logs`
--
ALTER TABLE `appraisals_business_logs`
  ADD KEY `FK_applications_applications_logs` (`id`);

--
-- Indexes for table `appraisals_social`
--
ALTER TABLE `appraisals_social`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_social_appraisal` (`application_id`);

--
-- Indexes for table `appraisals_social_logs`
--
ALTER TABLE `appraisals_social_logs`
  ADD KEY `FK_applications_applications_logs` (`id`);

--
-- Indexes for table `archive_reports`
--
ALTER TABLE `archive_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_projects_archive_reports` (`project_id`),
  ADD KEY `FK_activities_archive_reports` (`activity_id`),
  ADD KEY `FK_products_archive_reports` (`product_id`),
  ADD KEY `FK_regions_archive_reports` (`region_id`),
  ADD KEY `FK_areas_archive_reports` (`area_id`),
  ADD KEY `FK_branches_archive_reports` (`branch_id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_index_areas` (`code`),
  ADD KEY `FK_regions_areas` (`region_id`);

--
-- Indexes for table `audit_data`
--
ALTER TABLE `audit_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_data_entry_id` (`entry_id`);

--
-- Indexes for table `audit_entry`
--
ALTER TABLE `audit_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_route` (`route`);

--
-- Indexes for table `authorization_codes`
--
ALTER TABLE `authorization_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Indexes for table `auth_assignment_`
--
ALTER TABLE `auth_assignment_`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name_index_auth_item` (`rule_name`),
  ADD KEY `type_index_auth_item` (`type`);

--
-- Indexes for table `auth_item_`
--
ALTER TABLE `auth_item_`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name_index_auth_item` (`rule_name`),
  ADD KEY `type_index_auth_item` (`type`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child_index_auth_item_child` (`child`);

--
-- Indexes for table `auth_item_child_`
--
ALTER TABLE `auth_item_child_`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child_index_auth_item_child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `auth_rule_`
--
ALTER TABLE `auth_rule_`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `awp`
--
ALTER TABLE `awp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monthly_closed_loans` (`monthly_closed_loans`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `no_of_loans` (`no_of_loans`),
  ADD KEY `monthly_recovery` (`monthly_recovery`);

--
-- Indexes for table `awp_branch_sustainability`
--
ALTER TABLE `awp_branch_sustainability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awp_loan_management_cost`
--
ALTER TABLE `awp_loan_management_cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awp_overdue`
--
ALTER TABLE `awp_overdue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awp_project_mapping`
--
ALTER TABLE `awp_project_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `awp_id` (`awp_id`),
  ADD KEY `monthly_closed_loans` (`monthly_closed_loans`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `awp_target_vs_achievement`
--
ALTER TABLE `awp_target_vs_achievement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ba_assets`
--
ALTER TABLE `ba_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ba_id_index_ba_fixed_business_assets` (`ba_id`),
  ADD KEY `FK_applications_ba_fixed_business_assets` (`application_id`);

--
-- Indexes for table `ba_business_expenses`
--
ALTER TABLE `ba_business_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ba_id_index_ba_business_expenses` (`ba_id`),
  ADD KEY `FK_applications_ba_business_expenses` (`application_id`);

--
-- Indexes for table `ba_details`
--
ALTER TABLE `ba_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_ba` (`application_id`),
  ADD KEY `ba_id` (`ba_id`);

--
-- Indexes for table `ba_fixed_business_assets`
--
ALTER TABLE `ba_fixed_business_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ba_id_index_ba_fixed_business_assets` (`ba_id`),
  ADD KEY `FK_applications_ba_fixed_business_assets` (`application_id`);

--
-- Indexes for table `ba_new_required_assets`
--
ALTER TABLE `ba_new_required_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ba_id_index_ba_required_assets` (`ba_id`),
  ADD KEY `FK_applications_ba_required_assets` (`application_id`);

--
-- Indexes for table `ba_running_capital`
--
ALTER TABLE `ba_running_capital`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ba_id_index_ba_existing_investment` (`ba_id`),
  ADD KEY `FK_applications_ba_existing_investment` (`application_id`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_index_branches` (`code`),
  ADD KEY `region_id_index_branches` (`region_id`),
  ADD KEY `area_id_index_branches` (`area_id`),
  ADD KEY `FK_districts_branches` (`district_id`),
  ADD KEY `FK_divisions_branches` (`division_id`),
  ADD KEY `FK_provinces_branches` (`province_id`),
  ADD KEY `FK_countries_branches` (`country_id`),
  ADD KEY `FK_credit_divisions_branches` (`cr_division_id`),
  ADD KEY `FK_cities_branches` (`city_id`);

--
-- Indexes for table `branch_account_mapping`
--
ALTER TABLE `branch_account_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_branches_branch_account_mapping` (`branch_id`),
  ADD KEY `FK_accounts_branch_account_mapping` (`account_id`);

--
-- Indexes for table `branch_projects_mapping`
--
ALTER TABLE `branch_projects_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id_index_branch_projects_mapping` (`project_id`),
  ADD KEY `branch_id_index_branch_projects_mapping` (`branch_id`);

--
-- Indexes for table `branch_projects_mapping_`
--
ALTER TABLE `branch_projects_mapping_`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id_index_branch_projects_mapping` (`project_id`),
  ADD KEY `branch_id_index_branch_projects_mapping` (`branch_id`);

--
-- Indexes for table `branch_requests`
--
ALTER TABLE `branch_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id_index_branch_requests` (`region_id`),
  ADD KEY `area_id_index_branch_requests` (`area_id`),
  ADD KEY `FK_districts_branch_requests` (`district_id`),
  ADD KEY `FK_divisions_branch_requests` (`division_id`),
  ADD KEY `FK_provinces_branch_requests` (`province_id`),
  ADD KEY `FK_countries_branch_requests` (`country_id`),
  ADD KEY `FK_credit_divisions_branch_requests` (`cr_division_id`),
  ADD KEY `FK_cities_branch_requests` (`city_id`);

--
-- Indexes for table `branch_request_actions`
--
ALTER TABLE `branch_request_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `business_appraisal`
--
ALTER TABLE `business_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_ba` (`application_id`);

--
-- Indexes for table `cih_transactions_mapping`
--
ALTER TABLE `cih_transactions_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_transactions_cih_transactions_mapping` (`transaction_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id_index_cities` (`province_id`);

--
-- Indexes for table `config_rules`
--
ALTER TABLE `config_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_divisions`
--
ALTER TABLE `credit_divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uu_id` (`uu_id`),
  ADD UNIQUE KEY `imei_no` (`imei_no`),
  ADD KEY `uu_id_index_devices` (`uu_id`),
  ADD KEY `imei_no_index_devices` (`imei_no`),
  ADD KEY `device_model_index_devices` (`device_model`);

--
-- Indexes for table `disbursements`
--
ALTER TABLE `disbursements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diseases`
--
ALTER TABLE `diseases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_id_index_districts` (`division_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id_index_divisions` (`province_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id_index_donations` (`loan_id`),
  ADD KEY `credit_index_donations` (`amount`),
  ADD KEY `branch_id_index_donations` (`branch_id`),
  ADD KEY `recv_date_index_donations` (`receive_date`),
  ADD KEY `FK_applications_donations` (`application_id`),
  ADD KEY `FK_projects_donations` (`project_id`),
  ADD KEY `FK_schedules_donations` (`schedule_id`);

--
-- Indexes for table `donations_logs`
--
ALTER TABLE `donations_logs`
  ADD KEY `FK_donations_donations_logs` (`id`);

--
-- Indexes for table `emails_list`
--
ALTER TABLE `emails_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails_list_details`
--
ALTER TABLE `emails_list_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_list_id` (`email_list_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id_index_fields` (`team_id`);

--
-- Indexes for table `field_areas`
--
ALTER TABLE `field_areas`
  ADD PRIMARY KEY (`name`,`team_id`);

--
-- Indexes for table `fund_requests`
--
ALTER TABLE `fund_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `fund_requests_details`
--
ALTER TABLE `fund_requests_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grp_no_index_groups` (`grp_no`),
  ADD KEY `FK_regions_groups` (`region_id`),
  ADD KEY `FK_areas_groups` (`area_id`),
  ADD KEY `FK_branches_groups` (`branch_id`),
  ADD KEY `group_type` (`grp_type`),
  ADD KEY `br_serial` (`br_serial`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `group_actions`
--
ALTER TABLE `group_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action` (`action`),
  ADD KEY `status` (`status`),
  ADD KEY `expiry_date` (`expiry_date`);

--
-- Indexes for table `guarantors`
--
ALTER TABLE `guarantors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `parent_type` (`parent_type`);

--
-- Indexes for table `lists`
--
ALTER TABLE `lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_name` (`list_name`);

--
-- Indexes for table `lists_`
--
ALTER TABLE `lists_`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id_index_loans` (`application_id`),
  ADD KEY `area_id_index_loans` (`area_id`),
  ADD KEY `branch_id_index_loans` (`branch_id`),
  ADD KEY `date_disbursed_index_loans` (`date_disbursed`),
  ADD KEY `loan_amount_index_loans` (`loan_amount`),
  ADD KEY `disbursement_id_index_loans` (`disbursement_id`),
  ADD KEY `region_id_index_loans` (`region_id`),
  ADD KEY `sanction_no_index_loans` (`sanction_no`),
  ADD KEY `status_index_loans` (`status`),
  ADD KEY `FK_projects_loans` (`project_id`),
  ADD KEY `FK_activities_loans` (`activity_id`),
  ADD KEY `FK_products_loans` (`product_id`),
  ADD KEY `FK_groups_loans` (`group_id`);

--
-- Indexes for table `loans_logs`
--
ALTER TABLE `loans_logs`
  ADD KEY `FK_loans_loans_logs` (`id`);

--
-- Indexes for table `loan_actions`
--
ALTER TABLE `loan_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action` (`action`),
  ADD KEY `status` (`status`),
  ADD KEY `expiry_date` (`expiry_date`);

--
-- Indexes for table `loan_tranches`
--
ALTER TABLE `loan_tranches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`,`tranch_no`,`date_disbursed`,`fund_request_id`,`deleted`,`created_by`);

--
-- Indexes for table `loan_tranches_actions`
--
ALTER TABLE `loan_tranches_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnic_index_members` (`cnic`),
  ADD KEY `status_index_members` (`status`),
  ADD KEY `marital_status_index_members` (`marital_status`),
  ADD KEY `gender_index_members` (`gender`),
  ADD KEY `full_name_index_members` (`full_name`),
  ADD KEY `created_by_index_members` (`created_by`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `members_address`
--
ALTER TABLE `members_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_members_address` (`member_id`);

--
-- Indexes for table `members_email`
--
ALTER TABLE `members_email`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_members_email` (`member_id`);

--
-- Indexes for table `members_logs`
--
ALTER TABLE `members_logs`
  ADD KEY `FK_members_members_logs` (`id`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `members_phone`
--
ALTER TABLE `members_phone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_members_phone` (`member_id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `mobile_permissions`
--
ALTER TABLE `mobile_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `mobile_screens`
--
ALTER TABLE `mobile_screens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_operations` (`application_id`),
  ADD KEY `FK_projects_operations` (`project_id`),
  ADD KEY `FK_loans_operations` (`loan_id`),
  ADD KEY `FK_regions_operations` (`region_id`),
  ADD KEY `FK_areas_operations` (`area_id`),
  ADD KEY `FK_branches_operations` (`branch_id`);

--
-- Indexes for table `operations_logs`
--
ALTER TABLE `operations_logs`
  ADD KEY `FK_operations_operations_logs` (`id`);

--
-- Indexes for table `operation_type`
--
ALTER TABLE `operation_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_activity_mapping`
--
ALTER TABLE `product_activity_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_activity_mapping_`
--
ALTER TABLE `product_activity_mapping_`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress_reports`
--
ALTER TABLE `progress_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `period_index_progress_reports` (`period`),
  ADD KEY `project_id_index_progress_reports` (`project_id`),
  ADD KEY `report_date_index_progress_reports` (`report_date`);

--
-- Indexes for table `progress_report_details`
--
ALTER TABLE `progress_report_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `progress_report_id_index_progress_report_details` (`progress_report_id`),
  ADD KEY `division_id_index_progress_report_details` (`division_id`),
  ADD KEY `region_id_index_progress_report_details` (`region_id`),
  ADD KEY `area_id_index_progress_report_details` (`area_id`),
  ADD KEY `branch_id_index_progress_report_details` (`branch_id`),
  ADD KEY `country_id_index_progress_report_details` (`country_id`),
  ADD KEY `province_id_index_progress_report_details` (`province_id`),
  ADD KEY `district_id_index_progress_report_details` (`district_id`),
  ADD KEY `city_id_index_progress_report_details` (`city_id`);

--
-- Indexes for table `progress_report_update`
--
ALTER TABLE `progress_report_update`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects_agriculture`
--
ALTER TABLE `projects_agriculture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects_disabled`
--
ALTER TABLE `projects_disabled`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_project_details_disabled` (`application_id`),
  ADD KEY `FK_loans_project_details_disabled` (`loan_id`);

--
-- Indexes for table `projects_tevta`
--
ALTER TABLE `projects_tevta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_project_details_tevta` (`application_id`),
  ADD KEY `FK_loans__project_details_tevta` (`loan_id`);

--
-- Indexes for table `project_appraisals_mapping`
--
ALTER TABLE `project_appraisals_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_product_mapping`
--
ALTER TABLE `project_product_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_countries_provinces` (`country_id`);

--
-- Indexes for table `recoveries`
--
ALTER TABLE `recoveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id_index_recoveries` (`region_id`),
  ADD KEY `area_id_index_recoveries` (`area_id`),
  ADD KEY `branch_id_index_recoveries` (`branch_id`),
  ADD KEY `loan_id_index_recoveries` (`loan_id`),
  ADD KEY `schedule_id_index_recoveries` (`schedule_id`),
  ADD KEY `credit_index_recoveries` (`amount`),
  ADD KEY `FK_applications_recoveries` (`application_id`),
  ADD KEY `FK_projects_recoveries` (`project_id`);

--
-- Indexes for table `recoveries_logs`
--
ALTER TABLE `recoveries_logs`
  ADD KEY `FK_recoveries_recoveries_logs` (`id`);

--
-- Indexes for table `recovery_errors`
--
ALTER TABLE `recovery_errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recovery_files`
--
ALTER TABLE `recovery_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cr_division_id_index_regions` (`cr_division_id`);

--
-- Indexes for table `right_off`
--
ALTER TABLE `right_off`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_id` (`loan_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `due_date_index_schedules` (`due_date`),
  ADD KEY `credit_index_schedules` (`credit`),
  ADD KEY `branch_id_index_schedules` (`branch_id`),
  ADD KEY `loan_id_index_schedules` (`loan_id`),
  ADD KEY `schdl_amnt_index_schedules` (`schdl_amnt`),
  ADD KEY `FK_applications_schedules` (`application_id`);

--
-- Indexes for table `section_fields_configs`
--
ALTER TABLE `section_fields_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_view_section_fields_section_fields_configs` (`field_id`);

--
-- Indexes for table `section_fields_configs_`
--
ALTER TABLE `section_fields_configs_`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_view_section_fields_section_fields_configs` (`field_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_appraisal`
--
ALTER TABLE `social_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_applications_social_appraisal` (`application_id`);

--
-- Indexes for table `social_appraisal_diseases_mapping`
--
ALTER TABLE `social_appraisal_diseases_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `social_appraisal_id` (`social_appraisal_id`);

--
-- Indexes for table `structure_transfer`
--
ALTER TABLE `structure_transfer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`obj_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id_index_teams` (`branch_id`);

--
-- Indexes for table `tehsils`
--
ALTER TABLE `tehsils`
  ADD PRIMARY KEY (`id`),
  ADD KEY `district_id` (`district_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_accounts_transactions` (`account_id`),
  ADD KEY `FK_regions_transactions` (`region_id`),
  ADD KEY `FK_areas_transactions` (`area_id`),
  ADD KEY `FK_branches_transactions` (`branch_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `username_index_users` (`username`),
  ADD KEY `email_index_users` (`email`),
  ADD KEY `FK_cities_users` (`city_id`),
  ADD KEY `cnic` (`cnic`) USING BTREE,
  ADD KEY `cnic_2` (`cnic`) USING BTREE;

--
-- Indexes for table `users_`
--
ALTER TABLE `users_`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `username_index_users` (`username`),
  ADD KEY `email_index_users` (`email`),
  ADD KEY `FK_cities_users` (`city_id`),
  ADD KEY `cnic` (`cnic`) USING BTREE,
  ADD KEY `cnic_2` (`cnic`) USING BTREE;

--
-- Indexes for table `users_migrated`
--
ALTER TABLE `users_migrated`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `username_index_users` (`username`),
  ADD KEY `email_index_users` (`email`),
  ADD KEY `FK_cities_users` (`city_id`),
  ADD KEY `cnic` (`cnic`) USING BTREE,
  ADD KEY `cnic_2` (`cnic`) USING BTREE;

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_devices_`
--
ALTER TABLE `user_devices_`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_hierarchy_change_request`
--
ALTER TABLE `user_hierarchy_change_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_projects_mapping`
--
ALTER TABLE `user_projects_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_projects_mapping_`
--
ALTER TABLE `user_projects_mapping_`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_structure_mapping`
--
ALTER TABLE `user_structure_mapping`
  ADD KEY `obj_id` (`obj_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_structure_mapping_`
--
ALTER TABLE `user_structure_mapping_`
  ADD KEY `obj_id` (`obj_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id_index_verification` (`application_id`),
  ADD KEY `status_index_verification` (`status`);

--
-- Indexes for table `versions`
--
ALTER TABLE `versions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `view_sections`
--
ALTER TABLE `view_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `view_sections_`
--
ALTER TABLE `view_sections_`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `view_section_fields`
--
ALTER TABLE `view_section_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_view_sections_view_section_fields` (`section_id`);

--
-- Indexes for table `view_section_fields_`
--
ALTER TABLE `view_section_fields_`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_view_sections_view_section_fields` (`section_id`),
  ADD KEY `sort_order` (`sort_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_tokens`
--
ALTER TABLE `access_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1497;
--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `accounts_test`
--
ALTER TABLE `accounts_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1445;
--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aging_reports`
--
ALTER TABLE `aging_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4964;
--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18331;
--
-- AUTO_INCREMENT for table `application_actions`
--
ALTER TABLE `application_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6245;
--
-- AUTO_INCREMENT for table `appraisals`
--
ALTER TABLE `appraisals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `appraisals_business`
--
ALTER TABLE `appraisals_business`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `appraisals_social`
--
ALTER TABLE `appraisals_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
--
-- AUTO_INCREMENT for table `archive_reports`
--
ALTER TABLE `archive_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
--
-- AUTO_INCREMENT for table `audit_data`
--
ALTER TABLE `audit_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `audit_entry`
--
ALTER TABLE `audit_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `authorization_codes`
--
ALTER TABLE `authorization_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1105;
--
-- AUTO_INCREMENT for table `awp`
--
ALTER TABLE `awp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9710;
--
-- AUTO_INCREMENT for table `awp_branch_sustainability`
--
ALTER TABLE `awp_branch_sustainability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15257;
--
-- AUTO_INCREMENT for table `awp_loan_management_cost`
--
ALTER TABLE `awp_loan_management_cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `awp_overdue`
--
ALTER TABLE `awp_overdue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=779;
--
-- AUTO_INCREMENT for table `awp_project_mapping`
--
ALTER TABLE `awp_project_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40431;
--
-- AUTO_INCREMENT for table `awp_target_vs_achievement`
--
ALTER TABLE `awp_target_vs_achievement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11873;
--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ba_assets`
--
ALTER TABLE `ba_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2134;
--
-- AUTO_INCREMENT for table `ba_business_expenses`
--
ALTER TABLE `ba_business_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;
--
-- AUTO_INCREMENT for table `ba_details`
--
ALTER TABLE `ba_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=573;
--
-- AUTO_INCREMENT for table `ba_fixed_business_assets`
--
ALTER TABLE `ba_fixed_business_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;
--
-- AUTO_INCREMENT for table `ba_new_required_assets`
--
ALTER TABLE `ba_new_required_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;
--
-- AUTO_INCREMENT for table `ba_running_capital`
--
ALTER TABLE `ba_running_capital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;
--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=809;
--
-- AUTO_INCREMENT for table `branch_account_mapping`
--
ALTER TABLE `branch_account_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `branch_projects_mapping`
--
ALTER TABLE `branch_projects_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3738;
--
-- AUTO_INCREMENT for table `branch_projects_mapping_`
--
ALTER TABLE `branch_projects_mapping_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1644;
--
-- AUTO_INCREMENT for table `branch_requests`
--
ALTER TABLE `branch_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `branch_request_actions`
--
ALTER TABLE `branch_request_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `business_appraisal`
--
ALTER TABLE `business_appraisal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=578;
--
-- AUTO_INCREMENT for table `cih_transactions_mapping`
--
ALTER TABLE `cih_transactions_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;
--
-- AUTO_INCREMENT for table `config_rules`
--
ALTER TABLE `config_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `credit_divisions`
--
ALTER TABLE `credit_divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `disbursements`
--
ALTER TABLE `disbursements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11929;
--
-- AUTO_INCREMENT for table `diseases`
--
ALTER TABLE `diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11548;
--
-- AUTO_INCREMENT for table `emails_list`
--
ALTER TABLE `emails_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `emails_list_details`
--
ALTER TABLE `emails_list_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3179;
--
-- AUTO_INCREMENT for table `fund_requests`
--
ALTER TABLE `fund_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;
--
-- AUTO_INCREMENT for table `fund_requests_details`
--
ALTER TABLE `fund_requests_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3162;
--
-- AUTO_INCREMENT for table `group_actions`
--
ALTER TABLE `group_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;
--
-- AUTO_INCREMENT for table `guarantors`
--
ALTER TABLE `guarantors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;
--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3413;
--
-- AUTO_INCREMENT for table `lists`
--
ALTER TABLE `lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2537;
--
-- AUTO_INCREMENT for table `lists_`
--
ALTER TABLE `lists_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14578;
--
-- AUTO_INCREMENT for table `loan_actions`
--
ALTER TABLE `loan_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=503;
--
-- AUTO_INCREMENT for table `loan_tranches`
--
ALTER TABLE `loan_tranches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15635;
--
-- AUTO_INCREMENT for table `members_address`
--
ALTER TABLE `members_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43434;
--
-- AUTO_INCREMENT for table `members_email`
--
ALTER TABLE `members_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;
--
-- AUTO_INCREMENT for table `members_phone`
--
ALTER TABLE `members_phone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38923;
--
-- AUTO_INCREMENT for table `mobile_permissions`
--
ALTER TABLE `mobile_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
--
-- AUTO_INCREMENT for table `mobile_screens`
--
ALTER TABLE `mobile_screens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `operations`
--
ALTER TABLE `operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5996;
--
-- AUTO_INCREMENT for table `operation_type`
--
ALTER TABLE `operation_type`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `product_activity_mapping`
--
ALTER TABLE `product_activity_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `product_activity_mapping_`
--
ALTER TABLE `product_activity_mapping_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `progress_reports`
--
ALTER TABLE `progress_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `progress_report_details`
--
ALTER TABLE `progress_report_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=795;
--
-- AUTO_INCREMENT for table `progress_report_update`
--
ALTER TABLE `progress_report_update`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `projects_agriculture`
--
ALTER TABLE `projects_agriculture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `projects_disabled`
--
ALTER TABLE `projects_disabled`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `projects_tevta`
--
ALTER TABLE `projects_tevta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;
--
-- AUTO_INCREMENT for table `project_appraisals_mapping`
--
ALTER TABLE `project_appraisals_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
--
-- AUTO_INCREMENT for table `project_product_mapping`
--
ALTER TABLE `project_product_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `recoveries`
--
ALTER TABLE `recoveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174423;
--
-- AUTO_INCREMENT for table `recovery_errors`
--
ALTER TABLE `recovery_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recovery_files`
--
ALTER TABLE `recovery_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `right_off`
--
ALTER TABLE `right_off`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216961;
--
-- AUTO_INCREMENT for table `section_fields_configs`
--
ALTER TABLE `section_fields_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1236;
--
-- AUTO_INCREMENT for table `section_fields_configs_`
--
ALTER TABLE `section_fields_configs_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2175;
--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1086;
--
-- AUTO_INCREMENT for table `social_appraisal`
--
ALTER TABLE `social_appraisal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=738;
--
-- AUTO_INCREMENT for table `social_appraisal_diseases_mapping`
--
ALTER TABLE `social_appraisal_diseases_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;
--
-- AUTO_INCREMENT for table `structure_transfer`
--
ALTER TABLE `structure_transfer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1591;
--
-- AUTO_INCREMENT for table `tehsils`
--
ALTER TABLE `tehsils`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4933;
--
-- AUTO_INCREMENT for table `users_`
--
ALTER TABLE `users_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4865;
--
-- AUTO_INCREMENT for table `users_migrated`
--
ALTER TABLE `users_migrated`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5014;
--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;
--
-- AUTO_INCREMENT for table `user_devices_`
--
ALTER TABLE `user_devices_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `user_hierarchy_change_request`
--
ALTER TABLE `user_hierarchy_change_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_projects_mapping`
--
ALTER TABLE `user_projects_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `user_projects_mapping_`
--
ALTER TABLE `user_projects_mapping_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `versions`
--
ALTER TABLE `versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `view_sections`
--
ALTER TABLE `view_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `view_sections_`
--
ALTER TABLE `view_sections_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1028;
--
-- AUTO_INCREMENT for table `view_section_fields`
--
ALTER TABLE `view_section_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `view_section_fields_`
--
ALTER TABLE `view_section_fields_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1118;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `FK_branches_accounts` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `FK_areas_applications` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `FK_branches_applications` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `FK_members_applications` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `FK_products_applications` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `FK_projects_applications` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `FK_regions_applications` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `applications_logs`
--
ALTER TABLE `applications_logs`
  ADD CONSTRAINT `FK_applications_applications_logs` FOREIGN KEY (`id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `archive_reports`
--
ALTER TABLE `archive_reports`
  ADD CONSTRAINT `FK_activities_archive_reports` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`),
  ADD CONSTRAINT `FK_areas_archive_reports` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `FK_branches_archive_reports` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `FK_products_archive_reports` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `FK_projects_archive_reports` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `FK_regions_archive_reports` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `FK_regions_areas` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `audit_data`
--
ALTER TABLE `audit_data`
  ADD CONSTRAINT `fk_audit_data_entry_id` FOREIGN KEY (`entry_id`) REFERENCES `audit_entry` (`id`);

--
-- Constraints for table `ba_assets`
--
ALTER TABLE `ba_assets`
  ADD CONSTRAINT `FK_applications_ba_assets` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_assets` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `ba_business_expenses`
--
ALTER TABLE `ba_business_expenses`
  ADD CONSTRAINT `FK_applications_ba_business_expenses` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_business_expenses` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `ba_details`
--
ALTER TABLE `ba_details`
  ADD CONSTRAINT `FK_applications_ba_details` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_details` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `ba_fixed_business_assets`
--
ALTER TABLE `ba_fixed_business_assets`
  ADD CONSTRAINT `FK_applications_ba_fixed_business_assets` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_fixed_business_assets` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `ba_new_required_assets`
--
ALTER TABLE `ba_new_required_assets`
  ADD CONSTRAINT `FK_applications_ba_required_assets` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_required_assets` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `ba_running_capital`
--
ALTER TABLE `ba_running_capital`
  ADD CONSTRAINT `FK_applications_ba_existing_investment` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `FK_ba_ba_existing_investment` FOREIGN KEY (`ba_id`) REFERENCES `business_appraisal` (`id`);

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `FK_areas_branches` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `FK_cities_branches` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `FK_countries_branches` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `FK_credit_divisions_branches` FOREIGN KEY (`cr_division_id`) REFERENCES `credit_divisions` (`id`),
  ADD CONSTRAINT `FK_districts_branches` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `FK_divisions_branches` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `FK_provinces_branches` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`),
  ADD CONSTRAINT `FK_regions_branches` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `branch_account_mapping`
--
ALTER TABLE `branch_account_mapping`
  ADD CONSTRAINT `FK_accounts_branch_account_mapping` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `FK_branches_branch_account_mapping` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `branch_projects_mapping_`
--
ALTER TABLE `branch_projects_mapping_`
  ADD CONSTRAINT `FK_branches_branch_projects_mapping` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `FK_projects_branch_projects_mapping` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `branch_requests`
--
ALTER TABLE `branch_requests`
  ADD CONSTRAINT `FK_areas_branch_requests` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `FK_cities_branch_requests` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `FK_countries_branch_requests` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `FK_credit_divisions_branch_requests` FOREIGN KEY (`cr_division_id`) REFERENCES `credit_divisions` (`id`),
  ADD CONSTRAINT `FK_districts_branch_requests` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `FK_divisions_branch_requests` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `FK_provinces_branch_requests` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`),
  ADD CONSTRAINT `FK_regions_branch_requests` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `business_appraisal`
--
ALTER TABLE `business_appraisal`
  ADD CONSTRAINT `FK_applications_ba` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `cih_transactions_mapping`
--
ALTER TABLE `cih_transactions_mapping`
  ADD CONSTRAINT `FK_transactions_cih_transactions_mapping` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`);

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `FK_provinces_cities` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `FK_divisions_districts` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`);

--
-- Constraints for table `divisions`
--
ALTER TABLE `divisions`
  ADD CONSTRAINT `FK_provinces_divisions` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
