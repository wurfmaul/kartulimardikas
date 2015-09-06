CREATE DATABASE IF NOT EXISTS `kartulimardikas` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `kartulimardikas`;

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(6) NOT NULL AUTO_INCREMENT COMMENT 'user id',
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(128) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `language` varchar(8) COLLATE utf8_bin NOT NULL COMMENT 'language of user interface',
  `rights` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'users rights: 0 = user, 1 = admin, 2 = superadmin',
  `date_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of registration',
  `date_lastsignin` timestamp NULL DEFAULT NULL COMMENT 'timestamp of latest signin',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion',
  PRIMARY KEY (`uid`),
  INDEX (`username`, `email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `algorithm` (
  `aid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'algorithm id',
  `uid` int(6) NOT NULL COMMENT 'the author''s user id',
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `long_description` varchar(4096) COLLATE utf8_bin DEFAULT NULL,
  `variables` blob COMMENT 'variable information in json format',
  `tree` longblob COMMENT 'script information in json format',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of creation',
  `date_lastedit` timestamp NULL DEFAULT NULL COMMENT 'timestamp of last modification',
  `date_publish` timestamp NULL DEFAULT NULL COMMENT 'timestamp of publication',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion',
  PRIMARY KEY (`aid`),
  CONSTRAINT FOREIGN KEY (`uid`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `tag` (
  `tag` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'The tag''s name',
  `aid` int(8) NOT NULL COMMENT 'The algorithm''s id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the tag is marked as deleted',
  PRIMARY KEY (`tag`,`aid`),
  CONSTRAINT FOREIGN KEY (`aid`) REFERENCES `algorithm`(`aid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
