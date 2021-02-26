-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 23, 2019 at 12:54 PM
-- Server version: 10.1.43-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `emcdp19_jobs`
--

-- --------------------------------------------------------

--
-- Table structure for table `notification_messages`
--

CREATE TABLE `notification_messages`
(
    `sl`                    int(11)                          NOT NULL,
    `type`                  varchar(50)                      NOT NULL COMMENT 'email/sms/app/web',
    `template_path`         varchar(100)                     NOT NULL COMMENT 'app/template/{domain}/template_name.html',
    `sender_name`           varchar(100)                     NOT NULL COMMENT '{name, email}',
    `sender_contact`        varchar(100)                     NOT NULL,
    `sender_details_json`   text                             NOT NULL,
    `receiver_name`         varchar(100)                     NOT NULL,
    `receiver_contact`      varchar(100)                     NOT NULL,
    `receiver_details_json` text                             NOT NULL,
    `subject`               varchar(1500) CHARACTER SET utf8 NOT NULL,
    `parameters_json`       text                             NOT NULL,
    `body`                  blob                             NOT NULL COMMENT 'Actual Body/Generated',
    `attachments_json`      text                             NOT NULL,
    `confirm_key`           varchar(32)                      NOT NULL,
    `time_schedule`         int(11)                          NOT NULL,
    `time_sent`             int(11)                          NOT NULL,
    `time_delivered`        int(11)                          NOT NULL,
    `time_read`             int(11)                          NOT NULL,
    `creator`               int(11)                          NOT NULL,
    `ip_long`               bigint(20)                       NOT NULL,
    `time_created`          int(11)                          NOT NULL,
    `time_updated`          int(11)                          NOT NULL,
    `time_deleted`          int(11)                          NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

--
-- Dumping data for table `notification_messages`
--

INSERT INTO `notification_messages` (`sl`, `time_deleted`)
VALUES (10000000, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notification_messages`
--
ALTER TABLE `notification_messages`
    ADD PRIMARY KEY (`sl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notification_messages`
--
ALTER TABLE `notification_messages`
    MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 10000001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
