-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 14, 2015 at 05:17 
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kartulimardikas`
--
CREATE DATABASE IF NOT EXISTS `kartulimardikas` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `kartulimardikas`;

-- --------------------------------------------------------

--
-- Table structure for table `algorithm`
--

DROP TABLE IF EXISTS `algorithm`;
CREATE TABLE IF NOT EXISTS `algorithm` (
  `aid` int(8) NOT NULL COMMENT 'algorithm id',
  `uid` int(6) NOT NULL COMMENT 'the author''s user id',
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `long_description` varchar(4096) COLLATE utf8_bin DEFAULT NULL,
  `variables` blob COMMENT 'variable information in json format',
  `tree` longblob COMMENT 'script information in json format',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of creation',
  `date_lastedit` timestamp NULL DEFAULT NULL COMMENT 'timestamp of last modification',
  `date_publish` timestamp NULL DEFAULT NULL COMMENT 'timestamp of publication',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `tag` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'The tag''s name',
  `aid` int(8) NOT NULL COMMENT 'The algorithm''s id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(6) NOT NULL COMMENT 'user id',
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(128) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `language` varchar(8) COLLATE utf8_bin NOT NULL COMMENT 'language of user interface',
  `rights` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'users rights: 0 = user, 1 = admin, 2 = superadmin',
  `date_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of registration',
  `date_lastsignin` timestamp NULL DEFAULT NULL COMMENT 'timestamp of latest signin',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `algorithm`
--
ALTER TABLE `algorithm`
ADD PRIMARY KEY (`aid`), ADD KEY `uid` (`uid`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
ADD PRIMARY KEY (`tag`,`aid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `algorithm`
--
ALTER TABLE `algorithm`
MODIFY `aid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'algorithm id',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `uid` int(6) NOT NULL AUTO_INCREMENT COMMENT 'user id',AUTO_INCREMENT=1;