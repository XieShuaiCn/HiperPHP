-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 19, 2018 at 01:05 PM
-- Server version: 5.7.21
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `HiperPHP`
--
CREATE DATABASE IF NOT EXISTS `HiperPHP` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `HiperPHP`;

-- --------------------------------------------------------

--
-- Table structure for table `Demo`
--

CREATE TABLE `Demo` (
  `ID` int(11) NOT NULL,
  `name` varchar(512) NOT NULL,
  `value` varchar(10240) DEFAULT NULL,
  `flag` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Demo`
--

INSERT INTO `Demo` (`ID`, `name`, `value`, `flag`) VALUES
(1, 'test1', '这是第一条测试信息。', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Demo`
--
ALTER TABLE `Demo`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Demo`
--
ALTER TABLE `Demo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
