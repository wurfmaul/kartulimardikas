-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2015 at 01:58 
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kartulimardikas`
--

-- --------------------------------------------------------

--
-- Table structure for table `algorithm`
--

CREATE TABLE IF NOT EXISTS `algorithm` (
  `aid`              INT(8)    NOT NULL  AUTO_INCREMENT
  COMMENT 'algorithm id',
  `uid`              INT(6)    NOT NULL
  COMMENT 'user id',
  `name`             VARCHAR(64)
                     COLLATE utf8_bin    DEFAULT NULL,
  `description`      VARCHAR(1024)
                     COLLATE utf8_bin    DEFAULT NULL,
  `long_description` VARCHAR(4096)
                     COLLATE utf8_bin    DEFAULT NULL,
  `variables`        BLOB COMMENT 'variable information in json format',
  `tree`             LONGBLOB COMMENT 'script information in json format',
  `source_html`      LONGBLOB COMMENT 'base64 hash of the scripts html',
  `creation`         TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
  COMMENT 'timestamp of creation',
  `lastedit`         TIMESTAMP NULL      DEFAULT NULL
  COMMENT 'timestamp of last modification',
  PRIMARY KEY (`aid`),
  KEY `uid` (`uid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  AUTO_INCREMENT = 1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid`          INT(6)           NOT NULL AUTO_INCREMENT
  COMMENT 'user id',
  `username`     VARCHAR(64)
                 COLLATE utf8_bin NOT NULL,
  `email`        VARCHAR(128)
                 COLLATE utf8_bin NOT NULL,
  `password`     VARCHAR(255)
                 COLLATE utf8_bin NOT NULL,
  `registration` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT 'timestamp of registration',
  `lastsignin`   TIMESTAMP        NULL     DEFAULT NULL
  COMMENT 'timestamp of latest signin',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  AUTO_INCREMENT = 1;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
