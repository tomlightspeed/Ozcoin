-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 19, 2011 at 06:22 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pushpool`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountBalance`
--

CREATE TABLE IF NOT EXISTS `accountBalance` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balance` varchar(40) DEFAULT NULL,
  `sendAddress` varchar(255) DEFAULT '',
  `paid` varchar(40) DEFAULT '0',
  `threshold` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  KEY `b_userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=351 ;

-- --------------------------------------------------------

--
-- Table structure for table `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `name` (`name`)
) ENGINE=MEMORY  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `networkBlocks`
--

CREATE TABLE IF NOT EXISTS `networkBlocks` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(255) NOT NULL,
  `timestamp` int(255) NOT NULL,
  `accountAddress` varchar(255) NOT NULL,
  `confirms` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blockNumber_index` (`blockNumber`),
  KEY `confirms_index` (`confirms`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=839 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `timestamp` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `pool_worker`
--

CREATE TABLE IF NOT EXISTS `pool_worker` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `username` char(50) DEFAULT NULL,
  `password` char(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `hashrate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `p_username` (`username`),
  KEY `username_userid` (`username`,`associatedUserId`),
  KEY `userid` (`associatedUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=686 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE IF NOT EXISTS `shares` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3600825 ;

-- --------------------------------------------------------

--
-- Table structure for table `shares_history`
--

CREATE TABLE IF NOT EXISTS `shares_history` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `counted` int(1) NOT NULL COMMENT 'BOOLEAN) Tells server if it used these shares for counting',
  `blockNumber` int(255) NOT NULL,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` double(23,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sh_blocknumber` (`blockNumber`),
  KEY `sh_counted` (`counted`),
  KEY `time` (`time`,`username`),
  KEY `upstream_result` (`upstream_result`),
  KEY `our_result_username` (`our_result`,`username`),
  KEY `counted_our_result_username_blocknumber` (`counted`,`our_result`,`username`,`blockNumber`),
  KEY `blockNumber_username` (`blockNumber`,`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3693337 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_sent`
--

CREATE TABLE IF NOT EXISTS `tmp_sent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(45) NOT NULL,
  `amount` decimal(16,8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `address` (`address`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='temporary table part of Ycros'' work' AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `webUsers`
--

CREATE TABLE IF NOT EXISTS `webUsers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `admin` int(1) NOT NULL,
  `username` varchar(40) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Assocaited email: used for validating users, and re-setting passwords',
  `emailAuthPin` varchar(10) NOT NULL COMMENT 'The pin required to authorize that email address',
  `secret` varchar(10) NOT NULL,
  `loggedIp` varchar(255) NOT NULL,
  `sessionTimeoutStamp` int(255) NOT NULL,
  `accountLocked` int(255) NOT NULL COMMENT 'This is the timestamp when the account will be unlocked(usually used to lock accounts that are trying to be bruteforced)',
  `accountFailedAttempts` int(2) NOT NULL COMMENT 'This counts the number of failed attempts for web login',
  `pin` varchar(255) NOT NULL COMMENT 'four digit pin to allow account changes',
  `share_count` int(11) DEFAULT NULL,
  `stale_share_count` int(11) DEFAULT NULL,
  `shares_this_round` int(11) DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `activeEmail` int(1) DEFAULT NULL,
  `hashrate` int(11) DEFAULT NULL,
  `donate_percent` varchar(11) DEFAULT '0',
  `round_estimate` varchar(40) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=318 ;
