-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2014 at 12:56 
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kartulimardikas`
--

-- --------------------------------------------------------

--
-- Table structure for table `algorithms`
--

DROP TABLE IF EXISTS `algorithms`;
CREATE TABLE IF NOT EXISTS `algorithms` (
  `aid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'algorithm id',
  `uid` int(6) NOT NULL COMMENT 'user id',
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `long_description` varchar(4096) COLLATE utf8_bin DEFAULT NULL,
  `variables` blob,
  `script` longblob,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `aid` (`aid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `tid` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_mapping`
--

DROP TABLE IF EXISTS `tag_mapping`;
CREATE TABLE IF NOT EXISTS `tag_mapping` (
  `aid` int(8) NOT NULL,
  `tid` int(6) NOT NULL,
  PRIMARY KEY (`aid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(6) NOT NULL AUTO_INCREMENT COMMENT 'user id',
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;