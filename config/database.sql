-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 30, 2015 at 01:39 
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kartulimardikas`
--
CREATE DATABASE IF NOT EXISTS `kartulimardikas`
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_bin;
USE `kartulimardikas`;

-- --------------------------------------------------------

--
-- Table structure for table `algorithm`
--

CREATE TABLE IF NOT EXISTS `algorithm` (
  `aid`              INT(8)    NOT NULL
  COMMENT 'algorithm id',
  `uid`              INT(6)    NOT NULL
  COMMENT 'the author''s user id',
  `name`             VARCHAR(64)
                     COLLATE utf8_bin   DEFAULT NULL,
  `description`      VARCHAR(1024)
                     COLLATE utf8_bin   DEFAULT NULL,
  `long_description` VARCHAR(4096)
                     COLLATE utf8_bin   DEFAULT NULL,
  `variables`        BLOB COMMENT 'variable information in json format',
  `tree`             LONGBLOB COMMENT 'script information in json format',
  `date_creation`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT 'timestamp of creation',
  `date_lastedit`    TIMESTAMP NULL     DEFAULT NULL
  COMMENT 'timestamp of last modification',
  `date_publish`     TIMESTAMP NULL     DEFAULT NULL
  COMMENT 'timestamp of publication'
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid`               INT(6)           NOT NULL
  COMMENT 'user id',
  `username`          VARCHAR(64)
                      COLLATE utf8_bin NOT NULL,
  `email`             VARCHAR(128)
                      COLLATE utf8_bin NOT NULL,
  `password`          VARCHAR(255)
                      COLLATE utf8_bin NOT NULL,
  `date_registration` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT 'timestamp of registration',
  `date_lastsignin`   TIMESTAMP        NULL     DEFAULT NULL
  COMMENT 'timestamp of latest signin'
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

--
-- Indexes for table `algorithm`
--
ALTER TABLE `algorithm`
ADD PRIMARY KEY (`aid`), ADD KEY `uid` (`uid`), ADD FULLTEXT KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);