-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 26, 2020 at 05:31 AM
-- Server version: 10.1.44-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `optim20_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_api_error`
--

CREATE TABLE `log_api_error` (
  `sl` int(11) NOT NULL,
  `tbl` varchar(20) NOT NULL,
  `col` varchar(20) NOT NULL,
  `tsl` int(11) NOT NULL,
  `value_ex` longtext CHARACTER SET utf8 NOT NULL,
  `value_new` longtext CHARACTER SET utf8 NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_api_error`
--

INSERT INTO `log_api_error` (`sl`, `tbl`, `col`, `tsl`, `value_ex`, `value_new`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', 0, '', '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_auto_update`
--

CREATE TABLE `log_auto_update` (
  `sl` int(11) NOT NULL,
  `tbl` varchar(100) NOT NULL,
  `col_json` varchar(500) NOT NULL,
  `row_total` int(11) NOT NULL,
  `time_notify` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_auto_update`
--

INSERT INTO `log_auto_update` (`sl`, `tbl`, `col_json`, `row_total`, `time_notify`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', 0, 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_history`
--

CREATE TABLE `log_history` (
  `sl` int(11) NOT NULL,
  `tbl` varchar(100) NOT NULL,
  `col` varchar(100) NOT NULL,
  `tsl` int(11) NOT NULL,
  `value_ex` longtext CHARACTER SET utf8 NOT NULL,
  `value_new` longtext CHARACTER SET utf8 NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_history`
--

INSERT INTO `log_history` (`sl`, `tbl`, `col`, `tsl`, `value_ex`, `value_new`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', 0, '', '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `sl` int(11) NOT NULL,
  `user_sl` int(20) NOT NULL,
  `login_by` varchar(20) NOT NULL COMMENT 'password/cookie/facebbok/google',
  `unique_key` varchar(100) NOT NULL,
  `time_logout` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_login`
--

INSERT INTO `log_login` (`sl`, `user_sl`, `login_by`, `unique_key`, `time_logout`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, 0, '', '', 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_query_error`
--

CREATE TABLE `log_query_error` (
  `sl` int(11) NOT NULL,
  `group_token` varchar(32) NOT NULL,
  `index_sl` int(11) NOT NULL,
  `php_self` varchar(500) NOT NULL,
  `line_no` int(11) NOT NULL,
  `class` varchar(500) NOT NULL,
  `function` varchar(500) NOT NULL,
  `sql_string` longtext CHARACTER SET utf8 NOT NULL,
  `error_message` varchar(500) NOT NULL,
  `time_notify` int(11) NOT NULL,
  `time_resolved` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_query_error`
--

INSERT INTO `log_query_error` (`sl`, `group_token`, `index_sl`, `php_self`, `line_no`, `class`, `function`, `sql_string`, `error_message`, `time_notify`, `time_resolved`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_files`
--

CREATE TABLE `system_files` (
  `sl` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(500) CHARACTER SET utf8 NOT NULL,
  `location` varchar(2000) CHARACTER SET utf8 NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_files`
--

INSERT INTO `system_files` (`sl`, `type`, `title`, `location`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_listing`
--

CREATE TABLE `system_listing` (
  `sl` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `key_txt` varchar(100) NOT NULL,
  `val` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `priority` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_listing`
--

INSERT INTO `system_listing` (`sl`, `type`, `key_txt`, `val`, `priority`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', '', 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_menu`
--

CREATE TABLE `system_menu` (
  `sl` int(11) NOT NULL,
  `parent_sl` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `route` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `priority` int(11) NOT NULL,
  `selctor_part` int(11) NOT NULL COMMENT 'Number of part on route selected for active the menu.',
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_menu`
--

INSERT INTO `system_menu` (`sl`, `parent_sl`, `type`, `title`, `route`, `icon`, `priority`, `selctor_part`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_permissions`
--

CREATE TABLE `system_permissions` (
  `sl` int(11) NOT NULL,
  `users_sl` int(11) NOT NULL COMMENT 'required',
  `module` varchar(100) NOT NULL,
  `has_permissions` varchar(5000) NOT NULL COMMENT 'As array',
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_permissions`
--

INSERT INTO `system_permissions` (`sl`, `users_sl`, `module`, `has_permissions`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, 0, '', '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_users`
--

CREATE TABLE `system_users` (
  `sl` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `nick_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `login_id` varchar(100) NOT NULL COMMENT 'User name/default login id',
  `login_password` varchar(32) NOT NULL,
  `default_email` varchar(100) NOT NULL,
  `default_contact` varchar(100) NOT NULL COMMENT 'phone/mobile',
  `google_id` varchar(100) NOT NULL,
  `facebook_id` varchar(100) NOT NULL,
  `linkdin_id` varchar(100) NOT NULL,
  `github_id` varchar(100) NOT NULL,
  `permission` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_users`
--

INSERT INTO `system_users` (`sl`, `type`, `name`, `nick_name`, `login_id`, `login_password`, `default_email`, `default_contact`, `google_id`, `facebook_id`, `linkdin_id`, `github_id`, `permission`, `status`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_users_password_reset`
--

CREATE TABLE `system_users_password_reset` (
  `sl` int(11) NOT NULL,
  `user_sl` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `count_code_try` int(11) NOT NULL,
  `code_next` varchar(32) NOT NULL,
  `count_password_try` int(11) NOT NULL,
  `status` varchar(20) NOT NULL COMMENT 'pending,success,failed',
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_users_password_reset`
--

INSERT INTO `system_users_password_reset` (`sl`, `user_sl`, `code`, `count_code_try`, `code_next`, `count_password_try`, `status`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, 0, '', 0, '', 0, '', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_users_tfa`
--

CREATE TABLE `system_users_tfa` (
  `sl` int(11) NOT NULL,
  `user_sl` int(11) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'sms, email, google-authenticator',
  `tfa_security` varchar(32) NOT NULL COMMENT 'security key/mobile no',
  `time_setup` int(11) NOT NULL,
  `time_last_login` int(11) NOT NULL,
  `remember_cookie` varchar(100) NOT NULL,
  `remember_time` varchar(100) NOT NULL,
  `verification_code_send` varchar(50) NOT NULL,
  `verification_code` varchar(50) NOT NULL COMMENT 'Having code = verified',
  `status` varchar(50) NOT NULL COMMENT 'pending/enable/disable',
  `creator` int(11) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `time_deleted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_users_tfa`
--

INSERT INTO `system_users_tfa` (`sl`, `user_sl`, `type`, `tfa_security`, `time_setup`, `time_last_login`, `remember_cookie`, `remember_time`, `verification_code_send`, `verification_code`, `status`, `creator`, `ip_long`, `time_created`, `time_updated`, `time_deleted`) VALUES
(10000000, 0, '', '', 0, 0, '', '', '', '', '', 0, 0, 0, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_api_error`
--
ALTER TABLE `log_api_error`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `log_auto_update`
--
ALTER TABLE `log_auto_update`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `log_history`
--
ALTER TABLE `log_history`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `log_query_error`
--
ALTER TABLE `log_query_error`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_files`
--
ALTER TABLE `system_files`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_listing`
--
ALTER TABLE `system_listing`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_menu`
--
ALTER TABLE `system_menu`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_permissions`
--
ALTER TABLE `system_permissions`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_users`
--
ALTER TABLE `system_users`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_users_password_reset`
--
ALTER TABLE `system_users_password_reset`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `system_users_tfa`
--
ALTER TABLE `system_users_tfa`
  ADD PRIMARY KEY (`sl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_api_error`
--
ALTER TABLE `log_api_error`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `log_auto_update`
--
ALTER TABLE `log_auto_update`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `log_history`
--
ALTER TABLE `log_history`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `log_query_error`
--
ALTER TABLE `log_query_error`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_files`
--
ALTER TABLE `system_files`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_listing`
--
ALTER TABLE `system_listing`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_menu`
--
ALTER TABLE `system_menu`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_permissions`
--
ALTER TABLE `system_permissions`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_users`
--
ALTER TABLE `system_users`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_users_password_reset`
--
ALTER TABLE `system_users_password_reset`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;

--
-- AUTO_INCREMENT for table `system_users_tfa`
--
ALTER TABLE `system_users_tfa`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
