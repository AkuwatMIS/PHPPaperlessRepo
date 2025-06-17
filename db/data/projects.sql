-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2019 at 08:00 AM
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
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `product_id`, `name`, `status`, `assigned_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted`) VALUES
(1, 1, 'Agriculture inputs', 1, 0, 0, 0, 0, 0, 0),
(2, 1, 'Artificial Jewelry', 1, 0, 0, 0, 0, 0, 0),
(3, 1, 'Auto workshop', 1, 0, 0, 0, 0, 0, 0),
(4, 1, 'Beauty salon & Cosmetics', 1, 0, 0, 0, 0, 0, 0),
(5, 1, 'Butcher shop', 1, 0, 0, 0, 0, 0, 0),
(6, 1, 'Clinics', 1, 0, 0, 0, 0, 0, 0),
(7, 1, 'Construction & Material', 1, 0, 0, 0, 0, 0, 0),
(8, 1, 'Crockery Business', 1, 0, 0, 0, 0, 0, 0),
(9, 1, 'Cycle Works', 1, 0, 0, 0, 0, 0, 0),
(10, 1, 'Dairy Industry', 1, 0, 0, 0, 0, 0, 0),
(11, 1, 'Decoration & gift item', 1, 0, 0, 0, 0, 0, 0),
(12, 1, 'Electronic & services', 1, 0, 0, 0, 0, 0, 0),
(13, 1, 'Embroidery', 1, 0, 0, 0, 0, 0, 0),
(14, 1, 'Foods Stuff', 1, 0, 0, 0, 0, 0, 0),
(15, 1, 'Furniture', 1, 0, 0, 0, 0, 0, 0),
(16, 1, 'Fruits & Vegetables', 1, 0, 0, 0, 0, 0, 0),
(17, 1, 'Garments', 1, 0, 0, 0, 0, 0, 0),
(18, 8, 'Housing Loan', 1, 0, 0, 0, 0, 0, 0),
(19, 1, 'Handicrafts', 1, 0, 0, 0, 0, 0, 0),
(20, 1, 'Home Appliances & services', 1, 0, 0, 0, 0, 0, 0),
(21, 1, 'Home Industry', 1, 0, 0, 0, 0, 0, 0),
(22, 1, 'Leather Industry', 1, 0, 0, 0, 0, 0, 0),
(23, 1, 'Live stock', 1, 0, 0, 0, 0, 0, 0),
(24, 1, 'Lubricant Business', 1, 0, 0, 0, 0, 0, 0),
(25, 1, 'Mechanical & Engineering works', 1, 0, 0, 0, 0, 0, 0),
(26, 1, 'Music & Instruments', 1, 0, 0, 0, 0, 0, 0),
(27, 1, 'Plastic Molding', 1, 0, 0, 0, 0, 0, 0),
(28, 1, 'Poultry Business', 1, 0, 0, 0, 0, 0, 0),
(29, 1, 'Scrap & Recycling Work', 1, 0, 0, 0, 0, 0, 0),
(30, 1, 'Sports Industry', 1, 0, 0, 0, 0, 0, 0),
(31, 1, 'Stamp paper & Composing', 1, 0, 0, 0, 0, 0, 0),
(32, 1, 'Stationery & Printing', 1, 0, 0, 0, 0, 0, 0),
(33, 1, 'Transportation', 1, 0, 0, 0, 0, 0, 0),
(34, 1, 'Tuition Centre', 1, 0, 0, 0, 0, 0, 0),
(35, 1, 'Vendor', 1, 0, 0, 0, 0, 0, 0),
(36, 9, 'Education Loan', 1, 0, 0, 0, 0, 0, 0),
(37, 5, 'Liberation Loan', 1, 0, 0, 0, 0, 0, 0),
(38, 2, 'Health Loan', 1, 0, 0, 0, 0, 0, 0),
(41, 7, 'Marriage Purpose', 1, 0, 0, 0, 0, 0, 0),
(42, 19, 'TAP (Construction)', 1, 0, 0, 0, 0, 0, 0);

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `inst_type`, `min`, `max`, `status`, `assigned_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted`) VALUES
(1, 'Enterprise Loan', 'ETP', 'Monthly', 1000, 10000000, 1, 0, 0, 0, 0, 1537339508, 0),
(2, 'Health Loan', 'HLT', 'Monthly', 1000, 50000, 1, 0, 0, 0, 0, 0, 0),
(5, 'Liberation Loan', 'LIB', 'Monthly', 1000, 200000, 1, 0, 0, 0, 0, 0, 0),
(7, 'Marriage Loan', 'MRG', 'Monthly', 1000, 50000, 1, 0, 0, 0, 0, 0, 0),
(8, 'Housing Loan', 'HBF', 'Monthly', 1000, 100000, 1, 0, 0, 0, 0, 0, 0),
(9, 'Education', 'EDU', 'Monthly', 1000, 200000, 1, 0, 0, 0, 0, 0, 0),
(17, 'Emergency Loan', 'EMG', 'Monthly', 0, 100000, 1, 0, 0, 0, 0, 0, 0),
(18, 'Silver Loan', 'SLV', 'Monthly', 10000, 1000000, 1, 0, 0, 0, 0, 0, 0),
(19, 'TAP', 'TAP', 'Monthly', 1000, 100000, 1, 0, 0, 0, 0, 0, 0);

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_table`, `name`, `code`, `donor`, `funding_line`, `started_date`, `logo`, `loan_amount_limit`, `description`, `status`, `assigned_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted`) VALUES
(1, '', 'CMSES-Punjab', 'PSIC', 'Punjab Small Industries Corportaion', 'D002', 0, 'psic.jpg', 50000, 'The CMSES was launched in November 2011 through an interest free revolving fund (now Rs. 10 Billion) with the aim to stimulate entrepreneurship and development among low-income communities. It is the largest public-private partnership of interest-free microfinance in Pakistan.', 1, 0, 0, 0, 0, 1537339143, 0),
(2, '', 'Akhuwat', 'Akhuwat', 'Akhuwat', 'D001', 0, 'akhuwat.png', 500000, 'Akhuwat Project was initiated in 2001 form mere donation of 10,000 rupees. The contributors for the foremost donation were the philanthropist who opened their hearts and resources for service of mankind. Since then the project is run by donation coming from generous donors and members of Akhuwat Member Donation Program.', 1, 0, 0, 0, 0, 0, 0),
(3, 'projects_agriculture', 'Agriculture E-Credit', 'Kissan', 'Kissan', 'D019', 0, 'kissan.jpg', 100000, 'This project was initiated with the collaboration of Agriculture Department Govt.. of Punjab for providing interest free loans exclusively to the small farmers ( holding less than 2.5 acres) in 22 Districts of Punjab. Maximum loan limit for Rabi crop is up to Rs. 25,000 per Acre  and for Kharief crop is up to 40,000 per Acre.', 1, 0, 0, 0, 0, 0, 0),
(4, '', 'PM-IFL', 'pmifl', 'PM-IFL', 'D011', 0, 'pmifl.jpg', 50000, 'This project was initiated with the collaboration of Pakistan Poverty Alleviation fund for providing interest free loans to needy and prospective entrepreneurs of 10 districts all over the Pakistan. Loans are being provided  in 10 Districts i.e. Karachi, Khairpur, Jhang, Khaplu, Skardu, Quetta, Hripur, Charsadda, Mansehra, Mangora. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(5, '', 'CMSES-GB', 'gb', 'Chief Minister Self Employment Scehme, GB', 'D015', 0, 'gb.png', 50000, 'This project was initiated with the collaboration of Youth affair and tourism department Gilgit Baltistan for providing interest free loans to needy and prospective entrepreneurs of Gilgit Baltistan. Maximum loan limit is up to Rs. 75,000.', 1, 0, 0, 0, 0, 0, 0),
(6, '', 'LendwithCare', 'lwc', '	\nlwc', 'D001', 0, 'lwc.jpg', 100000, 'This project was initiated with the collaboration of Care International UK for providing interest free loans to needy and prospective entrepreneurs of Lahore and Kasur Districts through crowd funding. Maximum loan limit is up to Rs. 100,000', 1, 0, 0, 0, 0, 0, 0),
(7, '', 'PIDSA', 'pidsa', 'PIDSA', 'D009', 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(8, '', 'Eisaar Kameti', 'ek', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(9, '', 'HIWS', 'hiws', NULL, NULL, 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(10, '', 'HardoMky', 'HardoMky', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated in collaboration with Dunya Foundation for providing interest-free loans as enterprise loans, liberation loans, housing loans and marriage loans. Also, loans are provided for carrying out optimal farming practises, pursuing education and medical care. The  average size for this loan is Rs. 10,000.', 0, 0, 0, 0, 0, 0, 0),
(11, '', 'AL-Noor Housing', 'AL-Noor', 'Akhuwat', 'D005', 0, 'akhuwat.png', 100000, 'This project was initiated with the collaboration of Al-Nur Umer welfare Trust for providing interest free loans to the needy people to repair or constriction of their house in city of the Lahore. Maximum loan limit is up to Rs. 100,000.', 1, 0, 0, 0, 0, 0, 0),
(12, '', 'Begum Kousar Rana', 'BGKR', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated by the donation of Mr. Atif Rana for providing  interest free loans to needy and prospective entrepreneurs of Lahore district. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(13, '', 'AHSAAN', 'AHSAAN', NULL, NULL, 0, 'ihsan.png', 50000, 'This project was initiated with the collaboration of Ihsan Trust  for providing interest free loans to needy and prospective entrepreneurs of Karachi and Tano ala yar Districts. Maximum loan limit is up to Rs. 50,000. ', 0, 0, 0, 0, 0, 0, 0),
(14, '', 'Aziz-ur-Rehman', 'AZIZ', NULL, NULL, 0, 'akhuwat.png', 50000, 'This project was initiated with the collaboration of Kohat Cement company limited for providing interest free loans to needy and prospective entrepreneurs of Kohat District. Maximum loan limit is up to Rs. 50,000.', 0, 0, 0, 0, 0, 0, 0),
(15, '', 'Kohat Cement', 'KHTC', 'Akhuwat', 'D001', 0, 'kohat.jpg', 50000, 'This project was initiated with the collaboration of Kohat Cement company limited for providing interest free loans to needy and prospective entrepreneurs of Kohat District. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(16, '', 'IK Foundation', 'IKF', 'Akhuwat', 'D001', 0, 'ikf.jpg', 50000, 'This project was initiated with the collaboration of Imran Khan Foundation for providing interest free loans to needy and prospective entrepreneurs of Nowshera  District. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(17, 'projects_tevta', 'TEVTA', 'TEVTA', 'Akhuwat', 'D001', 0, 'tevta.jpg', 100000, 'The Technical Education and Vocational Training Authority (TEVTA) partnered with AIM in October 2015 to create a credit pool of Rs. 500 million that would offer interest-free loans exclusively to TEVTA graduates. The scheme is being administrated from all Branches of AIM in Punjab and offers Interest-free loans ranging from Rs. 10,000 to Rs. 100,000.', 1, 0, 0, 0, 0, 0, 0),
(18, '', 'FATA', 'FATA', 'FATA', 'D021', 0, 'fata.jpg', 50000, 'This project was initiated with the collaboration of FATA Development Authority for providing interest free loans to needy and prospective entrepreneurs of FATA. Maximum loan limit is up to Rs. 50,000. ', 1, 0, 0, 0, 0, 0, 0),
(19, '', 'IICO', 'IICO', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(20, '', 'Rairhi', 'rairhi', NULL, NULL, 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(21, '', 'Faithful Foundation', 'FOF', 'Faithful Foundation', 'D007', 0, 'fof.jpg', 50000, 'This project was initiated with the collaboration of Foundation of Faithful for providing interest free loans to needy and prospective entrepreneurs of Mansehra District.  Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(22, '', 'Disabled', 'Disabled', 'Disabled', 'D018', 0, 'akhuwat.png', 50000, 'This project was initiated with the collaboration of Pakistan Association of Deaf for providing interest free loans exclusively to the registered deaf of Pakistan Association of deaf in Lahore, Sargodha, Faisalabad, Rawalpindi and Sheikhupora districts. Maximum loan limit is up to Rs. 50,000.', 0, 0, 0, 0, 0, 0, 0),
(23, '', 'Other', 'Others', 'Others', 'D020', 0, NULL, 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(24, '', 'PPAF', 'PPAF', 'Akhuwat', 'D001', 0, 'ppaf.png', 50000, 'Launched in June 2014, the federal government awarded Rs. 3.25 Billion to Pakistan Poverty Alleviation Fund that in turn works through partner organizations to provide microfinance to entrepreneurs all over Pakistan. AIM is one of the partners and has received Rs. 446 million for delivery of small interest free loans.', 1, 0, 0, 0, 0, 0, 0),
(25, '', 'Live stock ', 'ls', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, NULL, 0, 0, 0, 0, 0, 0, 0),
(26, 'projects_disabled', 'PSPA (Disabled)', 'PSPA', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated with the collaboration of Punjab Small Industries Corporation for providing interest free loans exclusively to the people with disabilities in Punjab. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(27, '', 'British Asian Trust', 'BAT', 'Akhuwat', 'D001', 0, 'bat.jpg', 50000, 'This project was initiated with the collaboration of British Asian Trust DFID-UK for providing interest free loans exclusively to needy and prospective women entrepreneurs of Karachi and rural areas of Sindh. Maximum loan limit is up to Rs. 25,000.', 1, 0, 0, 0, 0, 0, 0),
(28, '', 'Friends of Akhuwat', 'FOA', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated by the donation of Muhammad Nauman and Friends for providing  interest free loans to needy and prospective entrepreneurs of Bahalwalpur district. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(29, '', 'Qazi Foundation', 'QZF', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated with the collaboration of Qazi Foundation for providing interest free loans to needy and prospective entrepreneurs of Chakwal District. Maximum loan limit is up to Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0),
(30, '', 'Akhuwat USA', 'USA', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'Akhuwat USA is a registered company under the umbrella of Akhuwat. The funds collected by Akhuwat-USA are transferred to Akhuwat.  With the help of donations from Akhuwat-USA, 2 new branches of Akhuwat are operating in Sindh.', 1, 0, 0, 0, 0, 0, 0),
(31, '', 'PFUJ', 'PFUJ', 'Akhuwat', 'D001', 0, NULL, 50000, 'This project was initaited with the collaboration of Pakistan Federal Union of Journalists for interest-free loans to its members. The loans are provided to members for buying bikes, photogragy cameras and laptops.  Maximum limit of the loan is Rs. 50,000.', 0, 0, 0, 0, 0, 0, 0),
(32, '', 'SA Foundation', 'SAF', 'Akhuwat', 'D001', 0, NULL, 50000, 'The project is initiated in collaboration with Shahid Afridi Foundation for providing interest-free enterprise loans in area of Chambai. The Maximimum limit of Loan is Rs. 50,000.', 0, 0, 0, 0, 0, 0, 0),
(33, '', 'Louis Berger', 'louis', 'USA', 'D001', 0, NULL, 50000, 'This project was initiated with the collaboration of Louis Berger (USAID) for providing interest free loans exclusively to needy and prospective  entrepreneurs of  4 district Multan , Muzaffargarh, Lodhran and Bahawalpur.  Maximum loan limit is up to Rs. 50,000.', 0, 0, 0, 0, 0, 0, 0),
(34, '', 'Liberation', 'libration', 'Akhuwat', 'D001', 0, 'akhuwat.png', 50000, 'This project was initiated with the sole purpose of assisting those borrowers who are grappled in the chains of Interest-Loan for liberating themselves from the mounting interest and initial loan. The  maximum limit for this loan is Rs. 100,000.', 1, 0, 0, 0, 0, 0, 0),
(35, '', 'AJ & K', 'AjK', 'Government of Azad & Jammu Kashmir ', 'D012', 0, NULL, 50000, 'This project is initiated in collaboration with Azad Kashmir Small Industries Corporation for providing interest-free loans in Azad Kashmir region to prospective entrepreneurs. The maximum limit of loan is Rs. 50,000.', 1, 0, 0, 0, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
