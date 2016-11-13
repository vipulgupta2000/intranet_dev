-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 14, 2014 at 01:44 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `editor`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `catid` int(10) NOT NULL AUTO_INCREMENT,
  `catname` varchar(20) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `parentid` int(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `access` varchar(20) DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `submit` varchar(11) NOT NULL,
  `modify` varchar(11) NOT NULL,
  `search` varchar(11) NOT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`catid`, `catname`, `level`, `parentid`, `description`, `access`, `status`, `submit`, `modify`, `search`) VALUES
(1, 'General', 0, 0, 'all articles', '0', '', '1;2', '3', '1;2;3'),
(2, 'Technical', 0, 0, 'all tehcnical discussion', '0', 'draft', '0', '0', '0');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
